<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Itstructure\GridView\Actions\Delete;
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
            'cities' => City::citiesList(),
        ]);
    }

    public function update(Request $request, Category $category)
    {
        Category::find($category->id)->update([
            'url' => $request->input('url'),
            'city_id' => $request->input('city_id'),
            'address' => $request->input('address'),
            'phone1' => $request->input('phone1'),
            'phone2' => $request->input('phone2'),
            'email' => $request->input('email'),
            'email2' => $request->input('email2'),
            'mail_domain' => $request->input('mail_domain'),
            'YmetricaId' => $request->input('YmetricaId'),
            'VENYOOId' => $request->input('VENYOOId'),
            'GMiframe1' => $request->input('GMiframe1'),
            'GMiframe2' => $request->input('GMiframe2'),
            'crm' => $request->input('crm'),
            'crm_pass' => $request->input('crm_pass'),
            'crm_u' => $request->input('crm_u'),
        ]);

        return Redirect::route('categories');
    }

    public function destroy(Category $category)
    {
        $category = Category::findOrFail($category->id);
        $category->delete();

        return Redirect::route('categories');
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
                    'attribute' => 'city_id',
                    'label' => 'Город',
                    'value' => function ($row) {
                        return ($row->location) ? $row->location->city : "";
                    },
                    'filter' => [
                        'class' => DropdownFilter::class,
                        'name' => 'city_id', //for some reason works LIKE
                        'data' => City::citiesList(),
                    ],
                    'htmlAttributes' => [
                        'width' => '15%'
                    ]
                ],
                [
                    'attribute' => 'url',
                    'label' => 'Сайт',
                    'format' => 'html',
                    'value' => function ($row) {
                        return "<a href='http://" . $row->url . "' target='_blank' >" . $row->url . "</a>";
                    },
                    'filter' => [
                        'class' => DropdownFilter::class,
                        'name' => 'url',
                        'data' => Category::urlsList(),
                    ],
                ],


                [
                    'label' => 'Действия',
                    'class' => ActionColumn::class,
                    'actionTypes' => [ // Required
                        'view' => function ($data) {
                            return '/category/' . $data->id . '/view';
                        },
                        'edit' => function ($data) {
                            return '/category/' . $data->id . '/edit';
                        },
                        [
                            'class' => Delete::class, // Required
                            'url' => function ($data) { // Optional
                                return '/category/' . $data->id . '/destroy';
                            },
                            'htmlAttributes' => [ // Optional
                                'onclick' => 'return window.confirm("Вы уверены, что хотите удалить?");'
                            ],
                        ],
                        'htmlAttributes' => [ // Html attributes for <img> tag.
                            'width' => '350',
                        ]
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
