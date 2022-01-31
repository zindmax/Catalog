<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */
CModule::IncludeModule("iblock");

//$arIBlockType = CIBlockParameters::GetIBlockTypes();
//if (!empty($arCurrentValues['IBLOCK_TYPE']) ) {
//    $iblockFilter['TYPE'] = $arCurrentValues['IBLOCK_TYPE'];
//}
$iblockFilter = array('ACTIVE' => 'Y');
$arIBlock = array();
$arSort = array("ASC" => "По возростанию", "DESC" => "По убыванию");
$arSortFields = array(
    "ID" => "ID",
    "NAME" => "Название",
    "SORT" => "Индекс сортировки",
);
$rsIBlock = CIBlock::GetList(array(), $iblockFilter);
while ($arr = $rsIBlock->Fetch()) {
    $arIBlock[$arr['ID']] = $arr['ID'] . " " . $arr['NAME'];
}

$arPropList = array();
$rsProps = CIBlockProperty::GetList(array(),array(
    'ACTIVE' => 'Y',
    "IBLOCK_ID" => ($arCurrentValues["IBLOCK_ID"] ?? $arCurrentValues["ID"])));
while ($arProp = $rsProps->Fetch())
{
    $arPropList[$arProp['ID']] = $arProp['NAME'];
}

$arTemplateList = array();
$arTemplateInfo = CComponentUtil::GetTemplatesList('bitrix:system.pagenavigation');
foreach($arTemplateInfo as $template) {
    $arTemplateList[$template['NAME']] = $template['NAME']. " " . $template['TEMPLATE'];
}

//        $arTemplateList = array();
//        $arTemplateInfo = CComponentUtil::GetTemplatesList('bitrix:system.pagenavigation');
//        foreach($arTemplateInfo as $template) {
//            $arTemplateList[$template['NAME']] = $template['TEMPLATE'];
//        }
//        \Bitrix\Main\Diag\Debug::dump($arTemplateList);

$arComponentParameters = array(
    "PARAMETERS" => array(
        "IBLOCK_ID" => array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => "Инфоблок",
            "TYPE" => "LIST",
            "VALUES" => $arIBlock,
            "REFRESH" => "Y"
        ),
        "ELEMENTS_COUNT" => array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => "Количество элементов на странице",
            "TYPE" => "STRING",
            "DEFAULT" => "10"
        ),
        "ELEMENT_FILTER" => array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => "Свойство для фильтрации",
            "TYPE" => "LIST",
            "VALUES" => $arPropList,
        ),
        "SORT_FIELD" => array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => "Поле сортировки по умолчанию",
            "TYPE" => "LIST",
            "VALUES" => $arSortFields,
            "DEFAULT" => "ID"
        ),
        "SORT_ORDER" => array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => "Направление сортировки по умолчанию",
            "TYPE" => "LIST",
            "VALUES" => $arSort,
            "DEFAULT" => "ASC"
        ),
        "PAGER_TEMPLATE" => array(
            "PARENT" => "BASE",
            "NAME" => "Шаблон постраничной навигации",
            "TYPE" => "LIST",
            "VALUES" => $arTemplateList,
            "DEFAULT" => ".default"
        ),
        "CACHE_TIME" => array(
            "PARENT" => "CACHE_SETTINGS",
            "NAME" => "Время кеширования",
            "TYPE" => "STRING",
            "DEFAULT" => "36000"
        )
    )
);

//CIBlockParameters::AddPagerSettings(
//    $arComponentParameters,
//    'Элементы',  // $pager_title
//    false,       // $bDescNumbering
//    true        // $bShowAllParam
//);