<?php

namespace App\Http\Controllers\Admin\Charts;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Backpack\CRUD\app\Http\Controllers\ChartController;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;

/**
 * Class WeeklyUsersChartController
 * @package App\Http\Controllers\Admin\Charts
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class WeeklyUsersChartController extends ChartController
{
    public function setup()
    {
        $this->chart = new Chart();

        // MANDATORY. Set the labels for the dataset points
        $this->chart->labels([
            'Today',
        ]);

        // RECOMMENDED. Set URL that the ChartJS library should call, to get its data using AJAX.


        // OPTIONAL
        // $this->chart->minimalist(false);
        // $this->chart->displayLegend(true);
        $this->chart = new Chart();
        $product_line = [];
        $category_line = [];
        $brand_line = [];

        $month = ['Jan', 'Feb', 'Mar', 'Apr','May', 'Jun','july' , 'Aug','Sep','Oct','Nov','Dec'];
        for ($i = 1 ; $i<count($month) ;$i++){
            $product_line[] = Product::whereMonth('created_at', '=', '0'.$i)->count();
            $category_line[] = Category::whereMonth('created_at', '=', '0'.$i)->count();
            $brand_line[] = Brand::whereMonth('created_at', '=', '0'.$i)->count();
        }

        $this->chart->dataset('Monthly Product Created', 'line', $product_line)
            ->color('rgba(205, 32, 31, 1)')
            ->backgroundColor('rgba(205, 32, 31, 0.4)');
        $this->chart->dataset('Monthly Category Created', 'line', $category_line)
            ->color('rgba(70, 127, 208, 1)')
            ->backgroundColor('rgba(70, 127, 208, 0.4)');
        $this->chart->dataset('Monthly Brand Created', 'line', $brand_line)
            ->color('rgb(255, 193, 7)')
            ->backgroundColor('rgba(255, 193, 7, 0.4)');
//        $this->chart->dataset('Green', 'line', [1, 4, 7, 11])
//            ->color('rgb(66, 186, 150)')
//            ->backgroundColor('rgba(66, 186, 150, 0.4)');
//        $this->chart->dataset('Purple', 'line', [2, 10, 5, 3])
//            ->color('rgb(96, 92, 168)')
//            ->backgroundColor('rgba(96, 92, 168, 0.4)');

        // MANDATORY. Set the labels for the dataset points
        $this->chart->labels(['Jan', 'Feb', 'Mar', 'Apr','May', 'Jun','july' , 'Aug','Sep','Oct','Nov','Dec']);
        $this->chart->load(backpack_url('charts/weekly-users'));
    }

    /**
     * Respond to AJAX calls with all the chart data points.
     *
     * @return json
     */
     public function data()
     {
//         $users_created_today = Product::whereDate('created_at', today())->count();
//
//         $this->chart->dataset('Users Created', 'bar', [
//                     $users_created_today,
//                 ])
//             ->color('rgba(205, 32, 31, 1)')
//             ->backgroundColor('rgba(205, 32, 31, 0.4)');
     }
}
