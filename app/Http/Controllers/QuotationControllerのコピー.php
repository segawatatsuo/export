<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Model\User;
use App\Model\Product;
use App\Model\Limit;
use App\Model\Preference;
use App\Model\Quotation;
use App\Model\Quotation_detail;
use App\Model\Userinformation;
use App\Model\Payment_method;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Model\Quitation_serial_number;
use Illuminate\Support\Facades\Validator;

class QuotationController extends Controller
{
    public function quotation(Request $request)
    {
        $type = $request->type;
        //HTMLフォーム送信のnameがitemのものだけ取得
        $type = session()->get('type');

        //数量の制限値
        $fedex = Limit::where('Delivery_type', '=', 'fedex')->first(); //10～100
        $air1 = Limit::where('Delivery_type', '=', 'air1')->first();//101～200
        $air2 = Limit::where('Delivery_type', '=', 'air2')->first();//201～500
        $ship = Limit::where('Delivery_type', '=', 'ship')->first();//501～

        $ctn_total = 0;
        $quantity_total = 0;
        $PREMIUM_SILK = [];

        //全角数字を半角に
        $PREMIUM_SILK['PS01'] = mb_convert_kana($request->get('PS01'), "n");
        $PREMIUM_SILK['PS02'] = mb_convert_kana($request->get('PS02'), "n");
        $PREMIUM_SILK['PS03'] = mb_convert_kana($request->get('PS03'), "n");
        $PREMIUM_SILK['PS04'] = mb_convert_kana($request->get('PS04'), "n");
        $PREMIUM_SILK['PS05'] = mb_convert_kana($request->get('PS05'), "n");
        $ps_count = (int)$PREMIUM_SILK['PS01'] + (int)$PREMIUM_SILK['PS02'] + (int)$PREMIUM_SILK['PS03'] + (int)$PREMIUM_SILK['PS04'] + (int)$PREMIUM_SILK['PS05'];
        $ctn_total += $ps_count;
        $quantity_total += $ps_count * Product::whereproduct_code('PS01')->first()->units;

        $PREMIUM_SILK['QT01'] = mb_convert_kana($request->get('QT01'), "n");
        $PREMIUM_SILK['QT02'] = mb_convert_kana($request->get('QT02'), "n");
        $PREMIUM_SILK['QT03'] = mb_convert_kana($request->get('QT03'), "n");
        $PREMIUM_SILK['QT04'] = mb_convert_kana($request->get('QT04'), "n");
        $PREMIUM_SILK['QT05'] = mb_convert_kana($request->get('QT05'), "n");
        $qt_count = (int)$PREMIUM_SILK['QT01'] + (int)$PREMIUM_SILK['QT02'] + (int)$PREMIUM_SILK['QT03'] + (int)$PREMIUM_SILK['QT04'] + (int)$PREMIUM_SILK['QT05'];
        $ctn_total += $qt_count;
        $quantity_total += $qt_count * Product::whereproduct_code('QT01')->first()->units;

        $PREMIUM_SILK['DL01'] = mb_convert_kana($request->get('DL01'), "n");
        $PREMIUM_SILK['DL02'] = mb_convert_kana($request->get('DL02'), "n");
        $PREMIUM_SILK['DL03'] = mb_convert_kana($request->get('DL03'), "n");
        $PREMIUM_SILK['DL04'] = mb_convert_kana($request->get('DL04'), "n");
        $PREMIUM_SILK['DL05'] = mb_convert_kana($request->get('DL05'), "n");
        $dl_count = (int)$PREMIUM_SILK['DL01'] + (int)$PREMIUM_SILK['DL02'] + (int)$PREMIUM_SILK['DL03'] + (int)$PREMIUM_SILK['DL04'] + (int)$PREMIUM_SILK['DL05'];
        $ctn_total += $dl_count;
        $quantity_total += $dl_count * Product::whereproduct_code('DL01')->first()->units;

        $PREMIUM_SILK['DQ01'] = mb_convert_kana($request->get('DQ01'), "n");
        $PREMIUM_SILK['DQ02'] = mb_convert_kana($request->get('DQ02'), "n");
        $PREMIUM_SILK['DQ03'] = mb_convert_kana($request->get('DQ03'), "n");
        $PREMIUM_SILK['DQ04'] = mb_convert_kana($request->get('DQ04'), "n");
        $PREMIUM_SILK['DQ05'] = mb_convert_kana($request->get('DQ05'), "n");
        $dq_count = (int)$PREMIUM_SILK['DQ01'] + (int)$PREMIUM_SILK['DQ02'] + (int)$PREMIUM_SILK['DQ03'] + (int)$PREMIUM_SILK['DQ04'] + (int)$PREMIUM_SILK['DQ05'];
        $ctn_total += $dq_count;
        $quantity_total += $dq_count * Product::whereproduct_code('DQ01')->first()->units;

        /*
        各行の小計
        $ps_count
        $qt_count
        $dl_count
        $dq_count

        1行($ps_count)の数がfedex,air1,air2,shipのどれに当たるかを調べる
        その次にその中の個別の数量が最低数より下回った場合はエラーとする

        */

        //全注文数が合っているか
        if ($type == "fedex") {
            if ($fedex->lower_limit != $fedex->upper_limit) {
                if ($ctn_total < $fedex->lower_limit or $ctn_total > $fedex->upper_limit) {
                    return redirect()
                       ->route('fedex')
                       ->with('flash_message', 'Please Order between ' . $fedex->lower_limit . ' and ' . $fedex->upper_limit . ' cartons')
                       ->withInput();
                }
            } else {
                if ($ctn_total < $fedex->lower_limit or $ctn_total > $fedex->upper_limit) {
                    return redirect()
                        ->route('fedex')
                        ->with('flash_message', 'Please Order between ' . $fedex->lower_limit . ' and ' . $fedex->upper_limit . ' cartons')
                        ->withInput();
                }
            }
            //Fedexは在庫数以上の注文はできないようにする
            if ($PREMIUM_SILK['PS01'] > Product::whereproduct_code('PS01')->first()->stock) {
                return redirect()
                ->route('fedex')
                ->with('flash_message', 'PS01 should be ordered in quantities of '.Product::whereproduct_code('PS01')->first()->stock.' or less')
                ->withInput();
            }
            if ($PREMIUM_SILK['PS02'] > Product::whereproduct_code('PS02')->first()->stock) {
                return redirect()
                ->route('fedex')
                ->with('flash_message', 'PS02 should be ordered in quantities of '.Product::whereproduct_code('PS02')->first()->stock.' or less')
                ->withInput();
            }
            if ($PREMIUM_SILK['PS03'] > Product::whereproduct_code('PS03')->first()->stock) {
                return redirect()
                ->route('fedex')
                ->with('flash_message', 'PS03 should be ordered in quantities of '.Product::whereproduct_code('PS03')->first()->stock.' or less')
                ->withInput();
            }
            if ($PREMIUM_SILK['PS04'] > Product::whereproduct_code('PS04')->first()->stock) {
                return redirect()
                ->route('fedex')
                ->with('flash_message', 'PS04 should be ordered in quantities of '.Product::whereproduct_code('PS04')->first()->stock.' or less')
                ->withInput();
            }
            if ($PREMIUM_SILK['PS05'] > Product::whereproduct_code('PS05')->first()->stock) {
                return redirect()
                ->route('fedex')
                ->with('flash_message', 'PS05 should be ordered in quantities of '.Product::whereproduct_code('PS05')->first()->stock.' or less')
                ->withInput();
            }

            if ($PREMIUM_SILK['QT01'] > Product::whereproduct_code('QT01')->first()->stock) {
                return redirect()
                ->route('fedex')
                ->with('flash_message', 'QT01 should be ordered in quantities of '.Product::whereproduct_code('QT01')->first()->stock.' or less')
                ->withInput();
            }
            if ($PREMIUM_SILK['QT02'] > Product::whereproduct_code('QT02')->first()->stock) {
                return redirect()
                ->route('fedex')
                ->with('flash_message', 'QT02 should be ordered in quantities of '.Product::whereproduct_code('QT02')->first()->stock.' or less')
                ->withInput();
            }
            if ($PREMIUM_SILK['QT03'] > Product::whereproduct_code('QT03')->first()->stock) {
                return redirect()
                ->route('fedex')
                ->with('flash_message', 'QT03 should be ordered in quantities of '.Product::whereproduct_code('QT03')->first()->stock.' or less')
                ->withInput();
            }
            if ($PREMIUM_SILK['QT04'] > Product::whereproduct_code('QT04')->first()->stock) {
                return redirect()
                ->route('fedex')
                ->with('flash_message', 'QT04 should be ordered in quantities of '.Product::whereproduct_code('QT04')->first()->stock.' or less')
                ->withInput();
            }
            if ($PREMIUM_SILK['QT05'] > Product::whereproduct_code('QT05')->first()->stock) {
                return redirect()
                ->route('fedex')
                ->with('flash_message', 'QT05 should be ordered in quantities of '.Product::whereproduct_code('QT05')->first()->stock.' or less')
                ->withInput();
            }

            if ($PREMIUM_SILK['DL01'] > Product::whereproduct_code('DL01')->first()->stock) {
                return redirect()
                ->route('fedex')
                ->with('flash_message', 'DL01 should be ordered in quantities of '.Product::whereproduct_code('DL01')->first()->stock.' or less')
                ->withInput();
            }
            if ($PREMIUM_SILK['DL02'] > Product::whereproduct_code('DL02')->first()->stock) {
                return redirect()
                ->route('fedex')
                ->with('flash_message', 'DL02 should be ordered in quantities of '.Product::whereproduct_code('DL02')->first()->stock.' or less')
                ->withInput();
            }
            if ($PREMIUM_SILK['DL03'] > Product::whereproduct_code('DL03')->first()->stock) {
                return redirect()
                ->route('fedex')
                ->with('flash_message', 'DL03 should be ordered in quantities of '.Product::whereproduct_code('DL03')->first()->stock.' or less')
                ->withInput();
            }
            if ($PREMIUM_SILK['DL04'] > Product::whereproduct_code('DL04')->first()->stock) {
                return redirect()
                ->route('fedex')
                ->with('flash_message', 'DL04 should be ordered in quantities of '.Product::whereproduct_code('DL04')->first()->stock.' or less')
                ->withInput();
            }
            if ($PREMIUM_SILK['DL05'] > Product::whereproduct_code('DL05')->first()->stock) {
                return redirect()
                ->route('fedex')
                ->with('flash_message', 'DL05 should be ordered in quantities of '.Product::whereproduct_code('DL05')->first()->stock.' or less')
                ->withInput();
            }

            if ($PREMIUM_SILK['DQ01'] > Product::whereproduct_code('DQ01')->first()->stock) {
                return redirect()
                ->route('fedex')
                ->with('flash_message', 'DQ01 should be ordered in quantities of '.Product::whereproduct_code('DQ01')->first()->stock.' or less')
                ->withInput();
            }
            if ($PREMIUM_SILK['DQ02'] > Product::whereproduct_code('DQ02')->first()->stock) {
                return redirect()
                ->route('fedex')
                ->with('flash_message', 'DQ02 should be ordered in quantities of '.Product::whereproduct_code('DQ02')->first()->stock.' or less')
                ->withInput();
            }
            if ($PREMIUM_SILK['DQ03'] > Product::whereproduct_code('DQ03')->first()->stock) {
                return redirect()
                ->route('fedex')
                ->with('flash_message', 'DQ03 should be ordered in quantities of '.Product::whereproduct_code('DQ03')->first()->stock.' or less')
                ->withInput();
            }
            if ($PREMIUM_SILK['DQ04'] > Product::whereproduct_code('DQ04')->first()->stock) {
                return redirect()
                ->route('fedex')
                ->with('flash_message', 'DQ04 should be ordered in quantities of '.Product::whereproduct_code('DQ04')->first()->stock.' or less')
                ->withInput();
            }
            if ($PREMIUM_SILK['DQ05'] > Product::whereproduct_code('DQ05')->first()->stock) {
                return redirect()
                ->route('fedex')
                ->with('flash_message', 'DQ05 should be ordered in quantities of '.Product::whereproduct_code('DQ05')->first()->stock.' or less')
                ->withInput();
            }
        }

        $err=array();

        if ($type == "air") {
            //1アイテムは最低20カートン以上でなければ駄目
            foreach ($PREMIUM_SILK as $key => $val) {
                if ($val != "") {
                    $ctn = $val; //カートン数量
                    if ($ctn < Limit::whereDelivery_type("air1")->first()->Minimum_orders) {
                        array_push($err, 'At least ' . Limit::whereDelivery_type("air1")->first()->Minimum_orders . ' cartons per type, please.');
                        break;
                        /*
                        return redirect()
                            ->route('air')
                            ->with('flash_message', 'At least ' . Limit::whereDelivery_type("air1")->first()->Minimum_orders . ' cartons per type, please.')
                            ->withInput();
                        */
                    }
                }
            }

            //管理画面の最低数量と最大数量が違う場合
            if ($air1->lower_limit != $air2->upper_limit) {
                //1列（同じグループ＝PS,qt,ql,dq）の総合計が100カートン以下か499カートン以上の場合
                if ($ctn_total < $air1->lower_limit and $ctn_total > $air1->upper_limit) {
                    array_push($err, 'Please enter at Between ' . $air1->lower_limit . ' and ' . $air1->upper_limit . ' cartons in total');
                    /*
                    return redirect()
                        ->route('air')
                        ->with('flash_message', 'Please enter at Between ' . $air1->lower_limit . ' and ' . $air1->upper_limit . ' cartons in total')
                        ->withInput();
                    */
                } elseif ($ctn_total < $air2->lower_limit and $ctn_total > $air2->upper_limit) {
                    array_push($err, 'Please enter at Between ' . $air2->lower_limit . ' and ' . $air2->upper_limit . ' cartons in total');
                    /*
                    return redirect()
                        ->route('air')
                        ->with('flash_message', 'Please enter at Between ' . $air2->lower_limit . ' and ' . $air2->upper_limit . ' cartons in total')
                        ->withInput();
                    */
                }

                //1列の合計がair1の最小値(105)より小さい場合もエラー
                if (($ps_count!="" and $ps_count!=0 and $ps_count < $air1->lower_limit) or ($qt_count!="" and $qt_count!=0 and $qt_count < $air1->lower_limit) or ($dl_count!="" or $dl_count!=0 and $dl_count < $air1->lower_limit) or ($dq_count!="" or $dq_count!=0 and $dq_count < $air1->lower_limit)) {
                    array_push($err, 'Please make the total of one column more than ' . $air1->lower_limit . ' cartons.');
                    /*
                    return redirect()
                        ->route('air')
                        ->with('flash_message', 'Please make the total of one column more than ' . $air1->lower_limit . ' cartons.')
                        ->withInput();
                    */
                }
                //エラーメッセージ
                if (count($err)==1) {
                    return redirect()
                        ->route('air')
                        ->with('flash_message', $err[0])
                        ->withInput();
                } elseif (count($err)==2) {
                    return redirect()
                        ->route('air')
                        ->with('flash_message', $err[0]."<br>".$err[1])
                        ->withInput();
                } elseif (count($err)==3) {
                    return redirect()
                        ->route('air')
                        ->with('flash_message', $err[0]."<br>".$err[1]."<br>".$err[2])
                        ->withInput();
                }
            } else {
                //管理画面の最低数量と最大数量が同じ場合
                //1列（同じグループ＝PS,qt,ql,dq）の総合計が105カートン以下か499カートン以上の場合
                if ($ctn_total < $air1->lower_limit or $ctn_total > $air2->upper_limit) {
                    return redirect()
                        ->route('air')
                        ->with('flash_message', '4Please enter at Between ' . $air1->lower_limit . ' and ' . $air2->upper_limit . ' cartons in total')
                        ->withInput();
                }
                //1列の合計がair1の最小値(105)より小さい場合もエラー
                if ($ps_count!="" and $ps_count < $air1->lower_limit or $qt_count!="" and $qt_count < $air1->lower_limit or $dl_count!="" and $dl_count < $air1->lower_limit or $dq_count!="" and $dq_count < $air1->lower_limit) {
                    return redirect()
                        ->route('air')
                        ->with('flash_message', 'Please make the total of one column more than ' . $air1->lower_limit . ' cartons.')
                        ->withInput();
                }
            }
        }



        if ($type == "ship") {
            if ($ctn_total < $ship->lower_limit) {
                return redirect()
                    ->route('ship')
                    ->with('flash_message', 'Please enter at at ' . $ship->lower_limit . ' or above cartons in total')
                    ->withInput();
            }

            //1列（同じグループ）の合計が最低注文数20以下なら
            if (
                $ps_count != null and $ps_count  < Limit::whereDelivery_type("ship")->first()->Minimum_orders or
                $qt_count != null and $qt_count  < Limit::whereDelivery_type("ship")->first()->Minimum_orders or
                $dl_count != null and $dl_count  < Limit::whereDelivery_type("ship")->first()->Minimum_orders or
                $dq_count != null and $dq_count  < Limit::whereDelivery_type("ship")->first()->Minimum_orders
            ) {
                return redirect()
                    ->route('ship')
                    ->with('flash_message', 'At least ' . Limit::whereDelivery_type("ship")->first()->Minimum_orders . ' cartons per type, please.')
                    ->withInput();
            }

            //1アイテムは最低20カートン以上でなければ駄目
            foreach ($PREMIUM_SILK as $key => $val) {
                if ($val != "") {
                    $ctn = $val; //カートン数量
                    if ($ctn < Limit::whereDelivery_type("ship")->first()->Minimum_orders) {
                        return redirect()
                            ->route('ship')
                            ->with('flash_message', 'At least ' . Limit::whereDelivery_type("ship")->first()->Minimum_orders . ' cartons per type, please.')
                            ->withInput();
                    }
                }
            }
        }

        //itemを分解する
        $items = [];

        //計算式
        function set_item($hinban, $ctn, $tanka, $hinmei)
        {
            $quantity_total = 0;
            $amount_total = 0;
            $units = Product::whereproduct_code($hinban)->first()->units;
            $quantity = $ctn * $units; //本数(カートン数*$units本)
            $amount = $quantity * $tanka; //金額＝数量＊単価
            $quantity_total += $quantity; //本数合計
            $amount_total += $amount; //金額合計
            $data = [$hinban, $hinmei, $tanka, $ctn, $quantity, $amount];
            return $data;
        }

        foreach ($PREMIUM_SILK as $key => $val) {
            if ($val != "") {
                $hinban = $key; //品番
                $ctn = $val; //カートン数量
                $temp = Product::whereproduct_code($hinban)->first();
                $hinmei = $temp->category . ' ' . $temp->group . ' ' . $temp->kind;
                //fedexは金額１種類
                if ($type == "fedex") {
                    $tanka = Product::whereproduct_code($hinban)->first()->price_fedex;
                    $data = set_item($hinban, $ctn, $tanka, $hinmei);
                    array_push($items, $data);
                }

                //air
                if ($type == "air") {
                    //ps
                    if ($key == "PS01" or $key == "PS02" or $key == "PS03" or $key == "PS04" or $key == "PS05") {
                        //PSが105から204なら
                        if ($ps_count >= $air1->lower_limit and $ps_count <= $air1->upper_limit) {
                            $tanka = Product::whereproduct_code($hinban)->first()->price_air_1;
                            $data = set_item($hinban, $ctn, $tanka, $hinmei);
                            array_push($items, $data);
                        //PSが205から499なら
                        } elseif ($ps_count >= $air2->lower_limit and $ps_count <= $air2->upper_limit) {
                            $tanka = Product::whereproduct_code($hinban)->first()->price_air_2;
                            $data = set_item($hinban, $ctn, $tanka, $hinmei);
                            array_push($items, $data);
                        }
                    }

                    //qt
                    if ($key == "QT01" or $key == "QT02" or $key == "QT03" or $key == "QT04" or $key == "QT05") {
                        if ($qt_count >= $air1->lower_limit and $qt_count <= $air1->upper_limit) {
                            $tanka = Product::whereproduct_code($hinban)->first()->price_air_1;
                            $data = set_item($hinban, $ctn, $tanka, $hinmei);
                            array_push($items, $data);
                        } elseif ($qt_count >= $air2->lower_limit and $qt_count <= $air2->upper_limit) {
                            $tanka = Product::whereproduct_code($hinban)->first()->price_air_2;

                            $data = set_item($hinban, $ctn, $tanka, $hinmei);
                            array_push($items, $data);
                        }
                    }
                    //dl
                    if ($key == "DL01" or $key == "DL02" or $key == "DL03" or $key == "DL04" or $key == "DL05") {
                        if ($dl_count >= $air1->lower_limit and $dl_count <= $air1->upper_limit) {
                            $tanka = Product::whereproduct_code($hinban)->first()->price_air_1;
                            $data = set_item($hinban, $ctn, $tanka, $hinmei);
                            array_push($items, $data);
                        } elseif ($dl_count >= $air2->lower_limit and $dl_count <= $air2->upper_limit) {
                            $tanka = Product::whereproduct_code($hinban)->first()->price_air_2;
                            $data = set_item($hinban, $ctn, $tanka, $hinmei);
                            array_push($items, $data);
                        }
                    }
                    //dq
                    if ($key == "DQ01" or $key == "DQ02" or $key == "DQ03" or $key == "DQ04" or $key == "DQ05") {
                        if ($dq_count >= $air1->lower_limit and $dq_count <= $air1->upper_limit) {
                            $tanka = Product::whereproduct_code($hinban)->first()->price_air_1;
                            $data = set_item($hinban, $ctn, $tanka, $hinmei);
                            array_push($items, $data);
                        } elseif ($dq_count >= $air2->lower_limit and $dq_count <= $air2->upper_limit) {
                            $tanka = Product::whereproduct_code($hinban)->first()->price_air_2;
                            $data = set_item($hinban, $ctn, $tanka, $hinmei);
                            array_push($items, $data);
                        }
                    }
                }

                //ship
                if ($type == "ship") {
                    //ps
                    if ($key == "PS01" or $key == "PS02" or $key == "PS03" or $key == "PS04" or $key == "PS05") {
                        if ($ps_count >= $air1->lower_limit and $ps_count <= $air1->upper_limit) {
                            $tanka = Product::whereproduct_code($hinban)->first()->price_air_1;
                            $data = set_item($hinban, $ctn, $tanka, $hinmei);
                            array_push($items, $data);
                            continue;
                        } elseif ($ps_count >= $air2->lower_limit and $ps_count <= $air2->upper_limit) {
                            $tanka = Product::whereproduct_code($hinban)->first()->price_air_2;
                            $data = set_item($hinban, $ctn, $tanka, $hinmei);
                            array_push($items, $data);
                            continue;
                        } elseif ($ps_count >= $ship->lower_limit) {
                            $tanka = Product::whereproduct_code($hinban)->first()->price_ship;
                            $data = set_item($hinban, $ctn, $tanka, $hinmei);
                            array_push($items, $data);
                            continue;
                        }
                    }
                    //qt
                    if ($key == "QT01" or $key == "QT02" or $key == "QT03" or $key == "QT04" or $key == "QT05") {
                        if ($qt_count >= $air1->lower_limit and $qt_count <= $air1->upper_limit) {
                            $tanka = Product::whereproduct_code($hinban)->first()->price_air_1;
                            $data = set_item($hinban, $ctn, $tanka, $hinmei);
                            array_push($items, $data);
                            continue;
                        } elseif ($qt_count >= $air2->lower_limit and $qt_count <= $air2->upper_limit) {
                            $tanka = Product::whereproduct_code($hinban)->first()->price_air_2;
                            $data = set_item($hinban, $ctn, $tanka, $hinmei);
                            array_push($items, $data);
                            continue;
                        } elseif ($qt_count >= $ship->lower_limit) {
                            $tanka = Product::whereproduct_code($hinban)->first()->price_ship;
                            $data = set_item($hinban, $ctn, $tanka, $hinmei);
                            array_push($items, $data);
                            continue;
                        }
                    }
                    //dl
                    if ($key == "DL01" or $key == "DL02" or $key == "DL03" or $key == "DL04" or $key == "DL05") {
                        if ($dl_count >= $air1->lower_limit and $dl_count <= $air1->upper_limit) {
                            $tanka = Product::whereproduct_code($hinban)->first()->price_air_1;
                            $data = set_item($hinban, $ctn, $tanka, $hinmei);
                            array_push($items, $data);
                            continue;
                        } elseif ($dl_count >= $air2->lower_limit and $dl_count <= $air2->upper_limit) {
                            $tanka = Product::whereproduct_code($hinban)->first()->price_air_2;
                            $data = set_item($hinban, $ctn, $tanka, $hinmei);
                            array_push($items, $data);
                            continue;
                        } elseif ($dl_count >= $ship->lower_limit) {
                            $tanka = Product::whereproduct_code($hinban)->first()->price_ship;
                            $data = set_item($hinban, $ctn, $tanka, $hinmei);
                            array_push($items, $data);
                            continue;
                        }
                    }
                    //dq
                    if ($key == "DQ01" or $key == "DQ02" or $key == "DQ03" or $key == "DQ04" or $key == "DQ05") {
                        if ($dq_count >= $air1->lower_limit and $dq_count <= $air1->upper_limit) {
                            $tanka = Product::whereproduct_code($hinban)->first()->price_air_1;
                            $data = set_item($hinban, $ctn, $tanka, $hinmei);
                            array_push($items, $data);
                            continue;
                        } elseif ($dq_count >= $air2->lower_limit and $dq_count <= $air2->upper_limit) {
                            $tanka = Product::whereproduct_code($hinban)->first()->price_air_2;
                            $data = set_item($hinban, $ctn, $tanka, $hinmei);
                            array_push($items, $data);
                            continue;
                        } elseif ($dq_count >= $ship->lower_limit) {
                            $tanka = Product::whereproduct_code($hinban)->first()->price_ship;
                            $data = set_item($hinban, $ctn, $tanka, $hinmei);
                            array_push($items, $data);
                            continue;
                        }
                    }
                }
            }
        }

        //合計金額
        $amount_total = 0;
        foreach ($items as $val) {
            $amount_total += $val[5];
        }

        //アイテムをセッションに入れた
        session()->put('items', $items);

        //Sailing on(出航予定月)
        $date = new Carbon();
        $date = Carbon::now();
        if ($date->day <= 23) {
            $year = $date->format('Y');
            $month = $date->format('M');
            $sailing_on = $month . ',' . $year;
        } else {
            $date = $date->addMonth();
            $year = $date->format('Y');
            $month = $date->format('M');
            $sailing_on = $month . ',' . $year;
        }

        //ユニークキー（見積番号）を作成
        $uuid = strtoupper(uniqid());

        $preference_data = Preference::first();
        $user_id = Auth::id();



        //quotations(見積もり)テーブルにデータを作成する
        $db = new Quotation();

        //見積書番号作成
        $serial_number = new Quitation_serial_number();
        $latestOrder = Quitation_serial_number::orderBy('created_at', 'DESC')->first();
        if ($latestOrder === null) {
            $qt_number = '0001';
        } else {
            $qt_number = str_pad($latestOrder->id + 1, 4, "0", STR_PAD_LEFT);
        }

        $quotation_no = 'quitation_' . $qt_number;
        $db->quotation_no = $quotation_no;
        $serial_number->pdf_file_name = $quotation_no . '.pdf';
        $serial_number->user_id = $user_id;
        $serial_number->save();
        /////////////

        $db->date_of_issue = Carbon::now();
        $db->shipper = $preference_data->shipper;
        $db->consignee_no = $user_id;
        $db->port_of_loading = $preference_data->port_of_loading;
        $db->sailing_on = $sailing_on;
        //arriving_on
        $db->expiry = $preference_data->expiry;

        $db->quantity_total = $quantity_total;
        $db->ctn_total = $ctn_total;
        $db->amount_total = $amount_total;

        //初回の人はまだこの時点ではconsigneeデータがない
        if ($db->consignee) {
            $db->consignee = $consignee;
        }
        if ($db->final_destination) {
            $db->final_destination = $state . ',' . $country;
        }

        //配送方法
        $db->delivery_method = $type;

        //quotations(見積もり)テーブルに保存
        $db->save();

        foreach ($items as $item) {
            //見積もり明細テーブルに登録
            $sub = new Quotation_detail();

            $sub->quotation_id = $user_id;
            $sub->product_code = $item[0];
            $sub->product_name = $item[1];
            $sub->unit_price = $item[2];
            $sub->ctn = $item[3];
            $sub->quantity = $item[4];
            $sub->amount = $item[5];
            $sub->quotation_no = $quotation_no;
            $sub->quotation_id = $db->id;
            $sub->save();
        }

        //Userinformationsテーブルからマスターのidと同じuser_idを探し住所等を取り出す
        $Userinformations = User::find($user_id)->Userinformations;

        //Userinformationsがnullの場合（住所登録が住んでいない場合）なら、quotation_noを持たせて住所入力フォームへ移動
        if ($Userinformations == null) {
            return view('entryform', compact('uuid', 'user_id', 'quotation_no'));
        }

        //住所登録が済んでいる場合
        $consignee = $Userinformations->consignee;
        $address_line1 = $Userinformations->address_line1;
        $address_line2 = $Userinformations->address_line2;
        $city = $Userinformations->city;
        $state = $Userinformations->state;
        $country = $Userinformations->country;
        $country_codes = $Userinformations->country_codes;
        $phone = $Userinformations->phone;
        $fax = $Userinformations->fax;

        $user = array(
            'user_id' => $user_id, 'consignee' => $consignee, 'address_line1' => $address_line1,
            'address_line2' => $address_line2, 'city' => $city, 'state' => $state, 'country' => $country,
            'country_codes' => $country_codes, 'phone' => $phone, 'fax' => $fax, 'delivery_method' => $type,
            'quotation_no' => $quotation_no
        );

        //セッションにitemsを持たす
        $collection = collect($items);
        session()->put('items', $collection);

        //$b=new QuotationController;
        //dd($request);
        //$b->generate_quotation_pdf($request);

        //return view('quotation', compact('quotations', 'uuid', 'preference_data', 'items', 'ctn_total', 'quantity_total', 'amount_total', 'sailing_on', 'user', 'img_banner','quotation_no','type'));
        //return view('quotation', compact('quotations', 'uuid', 'preference_data', 'items', 'ctn_total', 'quantity_total', 'amount_total', 'sailing_on', 'user', 'quotation_no', 'type'));
        return view('quotation', compact('uuid', 'preference_data', 'items', 'ctn_total', 'quantity_total', 'amount_total', 'sailing_on', 'user', 'quotation_no', 'type'));
    }


    //見積書PDFの出力(FORMからhidenでuuidを受け取る)
    public function generate_quotation_pdf(Request $request)
    {
        $main = [];
        //送信formから
        $quotation_no = $request->get('quotation_no');

        //Quotationから見積り内容をget
        $quotations = Quotation::where('quotation_no', $quotation_no)->get();
        $quotations_sub = Quotation_detail::where('quotation_no', $quotation_no)->get();

        //送信formから
        $final_destination = $request->final_destination;
        //データベースに保存
        \DB::table('quotations')->where('quotation_no', $quotation_no)->update(['final_destination' => $final_destination]);

        //出力したものにチェックをつける
        $date = Carbon::now();
        \DB::table('quotations')->where('quotation_no', $quotation_no)->update(['create_PDF' => $date]);
        //Preferenceから

        $shipper = $quotations[0]->shipper;

        //consignee
        $user_id = Auth::id();
        $Userinformations = User::find($user_id)->Userinformations;
        $consignee = $Userinformations->consignee;
        $port_of_loading = $quotations[0]->port_of_loading;

        $final_destination = $final_destination;

        $sailing_on = $quotations[0]->sailing_on;
        $arriving_on = $quotations[0]->arriving_on;
        $expiry = $quotations[0]->expiry;

        $preference_data = "";

        $main = [$quotation_no, $preference_data, $shipper, $consignee, $port_of_loading, $final_destination, $sailing_on, $arriving_on, $expiry];

        $data = [];
        $items = [];

        foreach ($quotations_sub as $quotation) {
            $product_code = $quotation->product_code;
            $product_name = $quotation->product_name;
            $quantity = $quotation->quantity;
            $ctn = $quotation->ctn;
            $quantity = $quotation->quantity;
            $unit_price = $quotation->unit_price;
            $amount = $quotation->amount;
            $data = [$product_code, $product_name, $quantity, $ctn, $unit_price, $amount];
            array_push($items, $data);
        }

        $quantity_total = $quotations[0]->quantity_total;
        $ctn_total = $quotations[0]->ctn_total;
        $amount_total = $quotations[0]->amount_total;
        $total = [$quantity_total, $ctn_total, $amount_total];

        $image_path = storage_path('app/public/hamada.png');
        $image_data = base64_encode(file_get_contents($image_path));

        //レターヘッド画像 C:\xampp\htdocs\fedex\storage\app\public\head.png
        $image_path2 = storage_path('app/public/head.png');
        $image_data2 = base64_encode(file_get_contents($image_path2));

        $output = $quotation_no . '.pdf';

        //quotation_print.blade.phpを読み込む
        $pdf = \PDF::loadView('quotation_print', compact('image_data', 'main', 'items', 'total', 'quotation_no', 'image_data2'))->setPaper('a4')->setWarnings(false);

        Storage::disk('public')->put('pdf/' . $output, $pdf->output());

        return $pdf->download($output);
    }



    //見積書PDFの出力(FORMからhidenでuuidを受け取る)
    public function generate_quotation_pdf2(Request $request)
    {
        //振込先情報をセッションに入れる
        $payee = Payment_method::where('selection', '選択')->get();
        session(['bank' => $payee[0]['bank']]);
        session(['branch' => $payee[0]['branch']]);
        session(['swift_code' => $payee[0]['swift_code']]);
        session(['account' => $payee[0]['account']]);
        session(['name' => $payee[0]['name']]);

        $main = [];
        //送信formから
        $quotation_no = $request->get('quotation_no');

        //Quotationから見積り内容をget
        $quotations = Quotation::where('quotation_no', $quotation_no)->get();
        $quotations_sub = Quotation_detail::where('quotation_no', $quotation_no)->get();

        //pdf作成日
        $day = Carbon::createFromFormat('Y-m-d H:i:s', $quotations[0]->created_at)->format('Y-m-d');


        $shipper = $quotations[0]->shipper;


        //consignee
        $user_id = Auth::id();
        //$Userinformations = User::find($user_id)->Userinformations;
        $Userinformations = Userinformation::where('user_id', $user_id)->get();
        $consignee = $Userinformations[0]['consignee'];
        $port_of_loading = $quotations[0]->port_of_loading;

        $final_destination = $quotations[0]->final_destination;

        $sailing_on = $quotations[0]->sailing_on;
        $arriving_on = $quotations[0]->arriving_on;
        $expiry = $quotations[0]->expiry;

        $preference_data = "";

        $main = [$quotation_no, $preference_data, $shipper, $consignee, $port_of_loading, $final_destination, $sailing_on, $arriving_on, $expiry];


        $items = [];

        foreach ($quotations_sub as $quotation) {
            $product_code = $quotation->product_code;
            $product_name = $quotation->product_name;
            $quantity = $quotation->quantity;
            $ctn = $quotation->ctn;
            $quantity = $quotation->quantity;
            $unit_price = $quotation->unit_price;
            $amount = $quotation->amount;
            $data = [$product_code, $product_name, $quantity, $ctn, $unit_price, $amount];
            array_push($items, $data);
        }
        $quantity_total = $quotations[0]->quantity_total;
        $ctn_total = $quotations[0]->ctn_total;
        $amount_total = $quotations[0]->amount_total;
        $total = [$quantity_total, $ctn_total, $amount_total];

        //$image_path = storage_path('app/public/hamada.png');
        //$image_path = 'https://ccmedico.com/fedex/storage/premium-silk/hamada.png';
        $image_path = 'https://ccmedico.com/fedex/storage/hamada.png';
        $image_data = base64_encode(file_get_contents($image_path));

        //レターヘッド画像
        $image_path = storage_path('img/head.png');
        $image_data2 = base64_encode(file_get_contents($image_path));


        $output = $quotation_no . '.pdf';

        //pdf_printout.blade.phpを読み込む
        $pdf = \PDF::loadView('quotation_print', compact('image_data', 'main', 'items', 'total', 'quotation_no', 'image_data2', 'day'))->setPaper('a4')->setWarnings(false);

        //Storage::disk('public')->put('pdf/' . $output, $pdf->output());
        //download
        //return $pdf->download($output);
        //プレビュー
        return $pdf->stream($output);
    }
}
