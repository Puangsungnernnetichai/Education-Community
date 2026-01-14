<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\Prompt;
use App\Models\PromptVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromptController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('accessAdmin');

        $prompts = Prompt::query()
            ->with('activeVersion')
            ->orderBy('key')
            ->get();

        return view('admin.prompts.index', [
            'prompts' => $prompts,
        ]);
    }

    public function show(Request $request, Prompt $prompt)
    {
        $this->authorize('accessAdmin');

        $prompt->load([
            'activeVersion',
            'versions' => function ($q) {
                $q->orderByDesc('version');
            },
        ]);

        return view('admin.prompts.show', [
            'prompt' => $prompt,
        ]);
    }

    public function storeVersion(Request $request, Prompt $prompt)
    {
        $this->authorize('accessAdmin');

        $data = $request->validate([
            'content' => ['required', 'string', 'min:10', 'max:20000'],
        ]);

        DB::transaction(function () use ($request, $prompt, $data) {
            $nextVersion = (int) (PromptVersion::query()->where('prompt_id', $prompt->id)->max('version') ?? 0) + 1;

            $version = PromptVersion::create([
                'prompt_id' => $prompt->id,
                'version' => $nextVersion,
                'content' => $data['content'],
                'created_by' => $request->user()?->id,
            ]);

            // Default to activate new version if no active version yet.
            if ($prompt->active_prompt_version_id === null) {
                $prompt->update(['active_prompt_version_id' => $version->id]);
            }

            AdminAuditLog::record(
                $request->user(),
                'prompt.version_created',
                Prompt::class,
                $prompt->id,
                ['version' => $nextVersion]
            );
        });

        return redirect()->route('admin.prompts.show', $prompt)->with('toast', [
            'type' => 'success',
            'message' => 'Saved prompt version.',
        ]);
    }

    public function activate(Request $request, Prompt $prompt, PromptVersion $version)
    {
        $this->authorize('accessAdmin');

        abort_unless($version->prompt_id === $prompt->id, 404);

        $prompt->update([
            'active_prompt_version_id' => $version->id,
        ]);

        AdminAuditLog::record(
            $request->user(),
            'prompt.activated',
            Prompt::class,
            $prompt->id,
            ['version' => $version->version]
        );

        return redirect()->route('admin.prompts.show', $prompt)->with('toast', [
            'type' => 'success',
            'message' => 'Activated prompt v' . $version->version,
        ]);
    }
}
