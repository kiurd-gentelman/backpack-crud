@extends(backpack_view('blank'))



@section('content')
    @php
        $widgets['before_content'][] = [
            'type'        => 'jumbotron',
            'heading'     => trans('backpack::base.welcome'),
            'content'     => trans('backpack::base.use_sidebar'),
            'button_link' => backpack_url('logout'),
            'button_text' => trans('backpack::base.logout'),
        ];
    @endphp
    @php
        $userCount= 1000;
        $articleCount= 1000;
        $lastArticleDaysAgo= 500;
        $productCount= 500;
            Widget::add()->to('after_content')->type('div')->class('row')->content([
                // notice we use Widget::make() to add widgets as content (not in a group)
                Widget::make()
                    ->type('progress')
                    ->class('card border-0 text-white bg-primary')
                    ->progressClass('progress-bar')
                    ->value($userCount)
                    ->description('Registered users.')
                    ->progress(100*(int)$userCount/1000)
                    ->hint(1000-$userCount.' more until next milestone.'),
                // alternatively, to use widgets as content, we can use the same add() method,
                // but we need to use onlyHere() or remove() at the end
                Widget::add()
                    ->type('progress')
                    ->class('card border-0 text-white bg-success')
                    ->progressClass('progress-bar')
                    ->value($articleCount)
                    ->description('Articles.')
                    ->progress(80)
                    ->hint('Great! Don\'t stop.')
                    ->onlyHere(),
                // alternatively, you can just push the widget to a "hidden" group
                Widget::make()
                    ->group('hidden')
                    ->type('progress')
                    ->class('card border-0 text-white bg-warning')
                    ->value($lastArticleDaysAgo.' days')
                    ->progressClass('progress-bar')
                    ->description('Since last article.')
                    ->progress(30)
                    ->hint('Post an article every 3-4 days.'),
                // both Widget::make() and Widget::add() accept an array as a parameter
                // if you prefer defining your widgets as arrays
                Widget::make([
                    'type' => 'progress',
                    'class'=> 'card border-0 text-white bg-dark',
                    'progressClass' => 'progress-bar',
                    'value' => $productCount,
                    'description' => 'Products.',
                    'progress' => (int)$productCount/75*100,
                    'hint' => $productCount>75?'Try to stay under 75 products.':'Good. Good.',
                ]),
            ]);
    @endphp
    <p>Your custom HTML can live here</p>
    @php
        $widgets['after_content'][] =[
        'type'       => 'chart',
        'controller' => \App\Http\Controllers\Admin\Charts\WeeklyUsersChartController::class,

        // OPTIONALS

        'class'   => 'card mb-6',
        'wrapper' => ['class'=> 'col-md-12 col-12'] ,
        'content' => [
             'header' => 'Product',
             'body'   => 'This chart should make it obvious how many new users have signed up in the past 7 days.<br><br>',
         ],
    ];

    @endphp


@endsection
