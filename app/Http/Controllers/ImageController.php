<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ImageController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp,svg|max:8192',
            'old_image' => 'nullable|string',
            'folder' => 'nullable|string',
        ]);
    
     
        // Get the uploaded file
        $file = $request->file('image');
    
        // Get the optional folder from request
        $uploadFolder = $request->folder;
    
        // Generate unique file name
        $fileName = time() . '_' . $file->getClientOriginalName();
    
        // Base directory (ex: 'local', 'staging', 'production')
        $baseDirectory = config('filesystems.disks.digitalocean.directory_env');
    
        // Build the final file path
        $filePath = $baseDirectory
            ? $baseDirectory . '/' . ($uploadFolder ? $uploadFolder . '/' : '') . $fileName
            : ($uploadFolder ? $uploadFolder . '/' . $fileName : $fileName);
    
        $disk = Storage::disk('digitalocean');
    
        // Delete old image if exists
        if ($request->filled('old_image')) {
            $oldFilePath = $request->old_image;
            if ($disk->exists($oldFilePath)) {
                $disk->delete($oldFilePath);
            }
        }
    
        // Upload the new file
        $disk->put($filePath, file_get_contents($file));
    
        // Generate the file URL
        $url = config('filesystems.disks.digitalocean.url') . '/' . $filePath;
    
        return response()->json([
            'message' => 'Image uploaded successfully',
            'url' => $url,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
