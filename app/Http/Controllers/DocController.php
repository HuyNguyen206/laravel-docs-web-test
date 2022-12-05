<?php

namespace App\Http\Controllers;

use App\Document;
use Facades\App\Document as DocumentFacade;
use Illuminate\Http\Request;

class DocController extends Controller
{
    public function showRootDocs()
    {
        return redirect(route('docs.version', DEFAULT_VERSION));
    }

    public function showDocVersion(Request $request, $version, $page = null)
    {
        if (!in_array($version, Document::validVersions())) {
            return redirect(route('docs.version', [DEFAULT_VERSION, $version]));
        }

        if ($page === null) {
            return redirect(route('docs.version', [DEFAULT_VERSION, 'default']));
        }
        try {
            $parsedContent = DocumentFacade::get($version, $page);
            $parsedContent = $this->replaceVersion($version, $parsedContent);
        }catch (\Exception $exception) {
            abort(404);
        }

        return view('welcome', compact('parsedContent'));
    }

    /**
     * @param $version
     * @param $parsedContent
     * @return array|string|string[]
     */
    public function replaceVersion($version, $parsedContent): string|array
    {
        $parsedContent = str_replace('{{version}}', $version, $parsedContent);
        return $parsedContent;
    }
}
