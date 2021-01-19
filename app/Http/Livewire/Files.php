<?php

namespace App\Http\Livewire;

use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class Files extends Component
{
    public $files;

    public function delete(File $file)
    {
        if ($file->user_id === auth()->id()) {
            File::destroy($file->id);
            Storage::delete($file->path);
            session()->flash('message', 'File successfully deleted.');
        }
    }

    public function render()
    {
        $this->files = auth()->user()->files()->orderByDesc('created_at')->get();

        return view('livewire.files');
    }
}
