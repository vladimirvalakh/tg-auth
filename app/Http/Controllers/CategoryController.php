<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Itstructure\GridView\Actions\CustomHtmlTag;
use Itstructure\GridView\Columns\ActionColumn;
use Itstructure\GridView\DataProviders\EloquentDataProvider;
use App\Models\Site;
use App\Models\City;
use App\Models\Category;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Itstructure\GridView\Filters\DropdownFilter;

class CategoryController extends Controller
{
    public function callAction($method, $parameters)
    {
        $currentRole = auth()->user()->role;

        if (!$currentRole) {
            return view('set_role', [
                'user' => auth()->user(),
                'roles' => DB::table('roles')->pluck('name', 'id')->toArray(),
            ]);
        }

        return parent::callAction($method, $parameters);
    }

    public function view(Category $category)
    {
        return view('category/view', [
            'category' => $category
        ]);
    }

    public function edit(Category $category)
    {
        return view('category/edit', [
            'category' => $category,
            'categories' => Category::categoriesList(),
        ]);
    }

    public function update(Request $request, Category $category)
    {
        Category::find($category->id)->update([
            'name' => $request->input('name'),
            'parent_id' => $request->input('parent_id'),
        ]);

        return Redirect::route('categories');
    }

    public function destroy(Category $category)
    {
        $category = Category::findOrFail($category->id);
        $category->delete();

        return Redirect::route('categories');
    }


    public function add()
    {
        $currentRole = auth()->user()->role;

        $addCriteria = (
            $currentRole->slug == Role::MODERATOR_SLUG ||
            $currentRole->slug == Role::OWNER_SLUG ||
            $currentRole->slug == Role::ADMINISTRATOR_SLUG
        );

        if (!$addCriteria) {
            abort(403);
        }

        return view('category/add', [
            'categories' => Category::categoriesList(),
        ]);
    }

    public function store(Request $request)
    {
        $currentRole = auth()->user()->role;

        $updateCriteria = (
            $currentRole->slug == Role::MODERATOR_SLUG ||
            $currentRole->slug == Role::OWNER_SLUG ||
            $currentRole->slug == Role::ADMINISTRATOR_SLUG
        );

        if (!$updateCriteria) {
            abort(403);
        }

        Category::create([
            'name' => $request->input('name'),
            'parent_id' => $request->input('parent_id'),
        ]);

        return Redirect::route('categories')->with('success','Категория добавлена.');
    }


    public function list()
    {

        $currentRole = auth()->user()->role;

        if (!$currentRole) {
            return view('set_role');
        }

        $dataProvider = new EloquentDataProvider(Category::query());

        $gridData = [
            'dataProvider' => $dataProvider,
            'paginatorOptions' => [
                'pageName' => 'p'
            ],
            'rowsPerPage' => 100,
            'use_filters' => true,
            'useSendButtonAnyway' => false,
            'searchButtonLabel' => 'Поиск',
            'resetButtonLabel' => 'Сброс',

            'columnFields' => [
                [
                    'attribute' => 'name',
                    'label' => 'Название',
                    'format' => 'html',
                    'filter' => [
                        'class' => DropdownFilter::class,
                        'name' => 'name',
                        'data' => Category::namesList(),
                    ],
                ],
                [
                    'attribute' => 'parent_id',
                    'label' => 'Родительская категория',
                    'value' => function ($row) {
                        return ($row->parent) ? $row->parent->name : "";
                    },
                ],

                [
                    'label' => 'Действия',
                    'class' => ActionColumn::class,
                    'actionTypes' => [
                        [
                            'class' => CustomHtmlTag::class,
                            'url' => function ($data) {
                                return '/category/' . $data->id . '/view';
                            },
                            'htmlAttributes' => '<button type="button" class="btn btn-block btn-primary mb-1">Детали</button>',
                        ],
                        [
                            'class' => CustomHtmlTag::class,
                            'url' => function ($data) {
                                return '/category/' . $data->id . '/edit';
                            },
                            'htmlAttributes' => '<button type="button" class="btn btn-block btn-warning mb-1">Редактировать</button>',
                        ],
                        [
                            'class' => CustomHtmlTag::class,
                            'url' => function ($data) {
                                return '/category/' . $data->id . '/destroy';
                            },
                            'htmlAttributes' => '<button type="button" class="btn btn-block btn-danger mb-1">Удалить</button>',
                        ],
                    ],
                ],
            ],
        ];

        return view('dashboard', [
            'dataProvider' => $dataProvider,
            'gridData' => $gridData
        ]);
    }
}
