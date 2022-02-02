<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */
CModule::IncludeModule("iblock");

$iblockFilter = array('ACTIVE' => 'Y');
$arIBlock = array();
$arSort = array("ASC" => GetMessage("IBLOCK_ASC"), "DESC" => GetMessage("IBLOCK_DESC"));
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
    $arTemplateList[$template['NAME']] = $template['NAME'];
}

$arComponentParameters = array(
    "GROUPS" => array(
        "PAGER_SETTINGS" => array(
            "NAME" => "Настройки постраничной навигации",
            "800"
        )
    ),
    "PARAMETERS" => array(
        "IBLOCK_ID" => array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("IBLOCK_ID_NAME"),
            "TYPE" => "LIST",
            "VALUES" => $arIBlock,
            "REFRESH" => "Y"
        ),
        "ELEMENTS_COUNT" => array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("IBLOCK_LIST_CONT"),
            "TYPE" => "STRING",
            "DEFAULT" => "10"
        ),
        "ELEMENT_FILTER" => array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("IBLOCK_ELEMENT_FILTER"),
            "TYPE" => "LIST",
            "VALUES" => $arPropList,
        ),
        "PAGER_TEMPLATE" => array(
            "PARENT" => "PAGER_SETTINGS",
            "NAME" => GetMessage("IBLOCK_PAGENAV_TEMPLATE"),
            "TYPE" => "LIST",
            "VALUES" => $arTemplateList,
            "DEFAULT" => ".default"
        ),
        "CACHE_TIME" => array(
            "PARENT" => "CACHE_SETTINGS",
            "NAME" => GetMessage("I_BLOCK_CACHE_TIME"),
            "TYPE" => "STRING",
            "DEFAULT" => "360000"
        )
    )
);