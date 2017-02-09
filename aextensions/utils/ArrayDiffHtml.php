<?php
/**
 * 将两个
 * @author abosfreeman
 */


namespace bbts\feature\util;
use \bbts\App;
use \bbts\Util;


class ArrayDiffHtml
{
    public static $common_table_style = 'min-width:24px;border:1px solid;border-collapse:collapse;text-align:center;';
    public static $common_tr_style    = 'min-width:24px;border-collapse:collapse;text-align:center;';
    public static $common_td_style    = 'min-width:24px;border:1px solid;border-collapse:collapse;text-align:center;';
    public static $common_th_style    = 'min-width:24px;border:1px solid;border-collapse:collapse;text-align:center;';

    public static function toTable($original, $modified)
    {
        $diff1 = static::compare($original, $modified);
        $diff2 = static::compare($modified, $original);
        $dom   = new \DOMDocument("1.0", "UTF8");
        static::_generateTable($dom, $dom, $diff1, $diff2);
        return $dom->saveHTML();
    }

    public static function createBorderTable($dom, $with_style=false)
    {
        $color = implode('', [dechex(mt_rand(0,255)), dechex(mt_rand(0,255)), dechex(mt_rand(0,255))]);
        $ele = $dom->createElement('table');
        $with_style and $ele->setAttribute('style', static::$common_table_style."border-color:#{$color};");
        $ele->setAttribute('width', '80%');
        $ele->setAttribute('cellspacing', '0px');
        return $ele;
    }

    public static function createBorderTr($dom)
    {
        $ele = $dom->createElement('tr');
        $ele->setAttribute('style', static::$common_tr_style);
        $ele->setAttribute('cellspacing', '0px');
        return $ele;
    }

    public static function createBorderTd($dom)
    {
        $ele = $dom->createElement('td');
        $ele->setAttribute('style', static::$common_td_style);
        $ele->setAttribute('cellspacing', '0px');
        return $ele;
    }

    public static function createBorderTh($dom)
    {
        $ele = $dom->createElement('th');
        $ele->setAttribute('style', static::$common_th_style);
        $ele->setAttribute('cellspacing', '0px');
        return $ele;
    }

    protected static function _generateTable($dom, $parent, $old, $new)
    {
        $null_val  = '--';
        $both_arr  = [];
        $is_root   = $dom === $parent;
        $no_direct = true;
        $table     = static::createBorderTable($dom, $is_root);

        # 配置表头节点
        $thead_row   = static::createBorderTr($dom);
        $thead_empty = static::createBorderTd($dom);
        $thead_old   = static::createBorderTh($dom);
        $thead_new   = static::createBorderTh($dom);
        $th_old_text = $dom->createTextNode('变更前');
        $th_new_text = $dom->createTextNode('变更后');

        # 配置属性
        if (! $is_root) {
            $thead_empty->setAttribute('style', static::$common_td_style.'border-top:none;border-left:none;');
            $thead_old->setAttribute('style', static::$common_td_style.'border-top:none;');
            $thead_new->setAttribute('style', static::$common_td_style.'border-top:none;border-right:none;');
        }

        # 组装表头
        $thead_old->appendChild($th_old_text);
        $thead_new->appendChild($th_new_text);
        $thead_row->appendChild($thead_empty);
        $thead_row->appendChild($thead_old);
        $thead_row->appendChild($thead_new);
        $table->appendChild($thead_row);

        # 先处理不是两边都为数组的情况
        # 两边都不是数组且只有旧数据存在（代表删除）
        foreach ($old as $key => $value) {
            if (isset($new[$key])) {
                continue;
            }

            # 由于数据转换的关系， 有部分空数组在配置文件里的初始值不是数组
            # 用这个判断忽略 空数组与空值的区别
            if (is_array($value) and empty($value) and empty($new[$key])) {
                continue;
            }
            $no_direct = false;

            # 配置各个节点
            $tr          = static::createBorderTr($dom);
            $th          = static::createBorderTh($dom);
            $td_old      = static::createBorderTd($dom);
            $td_new      = static::createBorderTd($dom);
            $th_text     = $dom->createTextNode($key);
            $td_new_text = $dom->createTextNode("删除");
            $td_old->setAttribute('class', 't-del-old');
            $td_new->setAttribute('class', 't-del-new');

            if (is_array($value)) {
                $td_old_child = static::_generateTableOneSide($dom, $td_old, $value);
            } else {
                $td_old_child = $dom->createTextNode($value);
            }

            # 配置各种属性
            if (! $is_root) {
                $td_new->setAttribute('style', static::$common_td_style.'border-right:none;border-bottom:none;');
                $td_old->setAttribute('style', static::$common_td_style.'border-left:none;border-bottom:none;');
                $th->setAttribute('style', static::$common_td_style.'border-left:none;border-bottom:none;');
            }


            # 组装各个节点
            $th->appendChild($th_text);
            $td_new->appendChild($td_new_text);
            $td_old->appendChild($td_old_child);
            $tr->appendChild($th);
            $tr->appendChild($td_old);
            $tr->appendChild($td_new);
            $table->appendChild($tr);
        }

        # 两边都不是数组且以新数据为准（代表新增或修改）
        foreach ($new as $key => $value) {
            if (is_array($value) and isset($old[$key]) and is_array($old[$key])) {
                $both_arr[] = $key;
                continue;
            }

            # 由于数据转换的关系， 有部分空数组在配置文件里的初始值不是数组
            # 用这个判断忽略 空数组与空值的区别
            if (is_array($value) and empty($value) and empty($old[$key])) {
                continue;
            }
            $no_direct = false;

            # 配置各个节点
            $tr          = static::createBorderTr($dom);
            $th          = static::createBorderTh($dom);
            $td_old      = static::createBorderTd($dom);
            $td_new      = static::createBorderTd($dom);
            $th_text     = $dom->createTextNode($key);
            $td_old_text = $dom->createTextNode(
                isset($old[$key]) ? $old[$key] : '新增'
            );

            if (is_array($value)) {
                $td_new_child = static::_generateTableOneSide($dom, $td_new, $value);
            } else {
                $td_new_child = $dom->createTextNode($value);
            }
            $td_new->setAttribute('class', 't-new-modify-new');
            $td_old->setAttribute('class', 't-new-modify-old');

            # 配置各种属性
            if (! $is_root) {
                $td_new->setAttribute('style', static::$common_td_style.'border-right:none;border-bottom:none;');
                $td_old->setAttribute('style', static::$common_td_style.'border-left:none;border-bottom:none;');
                $th->setAttribute('style', static::$common_td_style.'border-left:none;border-bottom:none;');
            }

            # 组装各个节点
            $th->appendChild($th_text);
            $td_new->appendChild($td_new_child);
            $td_old->appendChild($td_old_text);
            $tr->appendChild($th);
            $tr->appendChild($td_old);
            $tr->appendChild($td_new);
            $table->appendChild($tr);
        }
        if ($no_direct) {
            $table->removeChild($thead_row);
        }

        # 处理两边都是数组的情况, 需要递归，确定不了是[新增|修改|删除]
        foreach ($both_arr as $key => $index) {
            # 配置各个节点
            $th      = static::createBorderTh($dom);
            $td      = static::createBorderTd($dom);
            $tr      = static::createBorderTr($dom);
            $th_text = $dom->createTextNode($index);

            # 设置节点属性
            $td->setAttribute('colspan', 2); # 这里需要放置一个新的 table， 所以 colspan 是2
            $td->setAttribute('class', 't-both-arr');

            # 配置各种属性
            if (! $is_root) {
                $td->setAttribute('style', static::$common_td_style.'border-left:none;border-bottom:none;border-right:none;');
                $th->setAttribute('style', static::$common_td_style.'border-left:none;border-bottom:none;');
            }

            # 组装各个节点
            $th->appendChild($th_text);
            $tr->appendChild($th);
            $tr->appendChild($td);
            $table->appendChild($tr);

            # 生成内容到 内容节点
            static::_generateTable($dom, $td, $old[$index], $new[$index]);
        }


        # 最后将本次的 table 存到 dom 去
        $parent->appendChild($table);
    }

    protected static function _generateTableOneSide($dom, $parent, $arr)
    {
        if (! count($arr)) {
            return $dom->createTextNode('空数组');
        }
        $table    = static::createBorderTable($dom);

        foreach ($arr as $key => $value) {

            # 配置各个节点
            $tr       = static::createBorderTr($dom);
            $th       = static::createBorderTh($dom);
            $td       = static::createBorderTd($dom);
            $th_text  = $dom->createTextNode($key);
            if (is_array($value)) {
                $td_child = static::_generateTableOneSide($dom, $td, $value);
            } else {
                $td_child = $dom->createTextNode($value);
            }

            # 配置各种属性
            $td->setAttribute('class', 't-one-side');
            $td->setAttribute('style', static::$common_td_style.'border-left:none;border-right:none;border-top:none;');
            $th->setAttribute('style', static::$common_td_style.'border-left:none;border-top:none;');

            # 组装节点
            $th->appendChild($th_text);
            $tr->appendChild($th);
            $td->appendChild($td_child);
            $tr->appendChild($td);
            $table->appendChild($tr);
        }
        return $table;
    }

    /**
     * 做了一些小修改，让方法比较易懂
     * @author abosfreeman
     *
     * @copyright http://stackoverflow.com/questions/3876435/recursive-array-diff
     * '''
     *  The implementation only handles two arrays at a time,
     *  but I do not think that really posses a problem.
     *  You could run the diff sequentially if you need the diff of 3 or more arrays at a time.
     *  Also this method uses key checks and does a loose verification.
     * '''
     * @param  [type] $aArray1 [description]
     * @param  [type] $aArray2 [description]
     * @return [type]          [description]
     */
    public static function compare($aArray1, $aArray2)
    {
        $aReturn = [];

        foreach ($aArray1 as $mKey => $mValue) {

            # 如果不存在
            if (! isset($aArray2[$mKey])) {
                $aReturn[$mKey] = $mValue;
                continue;
            }

            # 非数组比较好处理
            if (! is_array($mValue)) {
                # 值不同则记录
                if ($mValue != $aArray2[$mKey]) {
                    $aReturn[$mKey] = $mValue;
                }
                continue;
            }

            # 递归处理数组
            $aRecursiveDiff = static::compare($mValue, $aArray2[$mKey]);

            # 如果有返回值， 代表该数组有不同。
            if (count($aRecursiveDiff)) {
                $aReturn[$mKey] = $aRecursiveDiff;
            }
        }
        return $aReturn;
    }
}


