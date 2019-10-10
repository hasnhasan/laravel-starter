<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\BackendController;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade;
use Plank\Mediable\SourceAdapters\SourceAdapterInterface;

class CategoryController extends BackendController
{
    public function index()
    {
        $modulCategories = Category::whereNull('parent_id')->get()->groupBy('module');

        return view('backend::category.list', compact('modulCategories'));
    }
}
