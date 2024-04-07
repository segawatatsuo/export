<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'product_code',
        'jan_code',
        'asin',
        'category',
        'group',
        'color',
        'color_map',
        'capacity',
        'title_header',
        'product_name',
        'price',
        'packaging_height',
        'packaging_vertical',
        'packaging_width',
        'packaging_weight',
        'unit_of_length',
        'units',
        'unit_type',
        'stock',
        'deadline',
        'depiction1',
        'depiction2',
        'img1',
        'img2',
        'img3',
        'img4',
        'img5',
        'group_j',
        'color_j',
        'product_name_j',
        'sort_order',
        'brand_name',
        'spec',
        'unit',
        'number_of_pieces',
        'ean_code',
        'sku_code',
        'manufacturing_delivery_date',
        'stock_delivery_date',
        'price_regular',
        'price_fedex',
        'price_air_1',
        'price_air_2',
        'price_ship',
        'sds_file_upload',
        'explosives_un',
        'explosives_class',
        'fedex_commercial_invoice_text',
        'fedex_commercial_invoice_hs_code',
        'fedex_commercial_invoice_unit_value',
        'dangerous_goods_declaration_sipping_name',
        'dangerous_goods_declaration_box_type',
        'dangerous_goods_declaration_packing',
        'product_size_w',
        'product_size_d',
        'product_size_h',
        'product_weight',
        'input_number',
        'carton_size_w',
        'carton_size_d',
        'carton_size_h',
        'carton_weight_net',
        'carton_weight_gross',
        'carton_weight_m3',
    ];
    //保存したいカラム名が複数の場合


    //1対多リレーション
    //親側に子の名前のメソッドを作る
    public function Userinformations()
    {
        return $this->hasOne('App\Model\Userinformation');
    }

    public function Order_details()
    {
        return $this->belongsTo("App\Model\Order_detail", 'product_code');
    }

    //商品画像を引っ張ってくる
    public function imageList()
    {
        return $this->hasMany('App\Model\ImageList');
    }

    public function get_categorys()
    {
        //カテゴリーのユニークだけ(ここではAIRSTOCKINGだけだが、今後ネールなどが入ってくる) session('article')は'Air Stocking'など
        //戻り値は "category" => "Air Stocking"
        $categorys = $this::where('hidden_item', '!=', '1')->where('category', session('article'))->groupBy('category')->orderBy('sort_order', 'asc')->get(['category']);
        return $categorys;
    }

    public function get_groups()
    {
        //Air Stocking中分類 session('article')は'Air Stocking'など
        //戻り値は配列 "group" => "PREMIUM-SILK","group" => "PREMIUM-SILK QT","group" => "DIAMOND LEGS","group" => "DIAMOND LEGS DQ"
        //$groups = Product::where('hidden_item', '!=', '1')->where('category', session('article'))->groupBy('group')->orderBy('sort_order', 'asc')->get(['group']);        
        $groups = $this::where('hidden_item', '!=', '1')->where('category', session('article'))->groupBy('group')->orderBy('sort_order', 'asc')->get(['group']);
        return $groups;
    }

    public function get_items($groups)
    {
        //グループ別の商品配列
        $items = [];
        //戻り値例　$items[0][0]['product_name']は　"AIRSTOCKING PREMIER SILK 120G LIGHT NATURAL"
        foreach ($groups as $g) {
            $b = $this::where('hidden_item', '!=', '1')->where('group', $g->group)->orderBy('sort_order', 'asc')->get();
            array_push($items, $b);
        }
        return $items;
    }

    public function unique_groups($items)
    {
        $groups = [];
        foreach ($items as $item) {
            foreach ($item as $val) {
                array_push($groups, $val->group);
                $groups = array_unique($groups);
            }
        }
        return $groups;
        /* $groups
        array:4 [▼
        0 => "PREMIUM-SILK"
        1 => "PREMIUM-SILK QT"
        2 => "DIAMOND LEGS"
        3 => "DIAMOND LEGS DQ"
        ]
        */
    }

    public function get_code($items)
    {
        //　$codes　配列に全部の商品コード(PS01,PS02...)を取り出す
        $codes = [];
        foreach ($items as $item) {
            foreach ($item as $val) {
                $hoge = [$val->product_code => $val->group];
                $codes = array_merge($codes, $hoge);
            }
            return $codes;
        }
        /* $codes 結果
               array:20 [▼
               "PS01" => "PREMIUM-SILK"
               "PS02" => "PREMIUM-SILK"
               "PS03" => "PREMIUM-SILK"
               "PS04" => "PREMIUM-SILK"
               "PS05" => "PREMIUM-SILK"
               "QT01" => "PREMIUM-SILK QT"
               "QT02" => "PREMIUM-SILK QT"
               "QT03" => "PREMIUM-SILK QT"
               "QT04" => "PREMIUM-SILK QT"
               "QT05" => "PREMIUM-SILK QT"
               "DL01" => "DIAMOND LEGS"
               "DL02" => "DIAMOND LEGS"
               "DL03" => "DIAMOND LEGS"
               "DL04" => "DIAMOND LEGS"
               "DL05" => "DIAMOND LEGS"
               "DQ01" => "DIAMOND LEGS DQ"
               "DQ02" => "DIAMOND LEGS DQ"
               "DQ03" => "DIAMOND LEGS DQ"
               "DQ04" => "DIAMOND LEGS DQ"
               "DQ05" => "DIAMOND LEGS DQ"
               ]
               */
    }
}
