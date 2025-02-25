<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\ImageOptimizer\OptimizerChainFactory;

class ImageController extends Controller
{
    public function optimizeImagesInDirectory()
    {
        $pathToDirectory = public_path('img');

        $files = glob($pathToDirectory . '/*.{jpg,png,gif,jpeg}', GLOB_BRACE);

        $optimizedCount = 0;

        foreach($files as $file) {
            $optimizerChain = OptimizerChainFactory::create();
            $result = $optimizerChain->optimize($file);

            if ($result) {
                // Image was optimized successfully
                $optimizedCount++;
            } else {
                // Image optimization failed
                return redirect()->back()->with('error', 'Failed to optimize images.');
            }
        }

        // If we reached here, all images were optimized successfully
        return redirect()->back()->with('success', $optimizedCount . ' images optimized successfully.');
    }
}
