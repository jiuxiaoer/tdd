<?php
/**
 * 项目辅助文件
 * Auther: yinshen
 * url:https://www.79xj.cn
 * 创建时间：2022/6/26 15:50
 */


function route_class() {
    return str_replace('.', '-', Route::currentRouteName());
}
