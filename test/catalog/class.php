<?
use \Bitrix\Main\Loader;
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

class CTestCatalog extends CBitrixComponent {


    public function onPrepareComponentParams($arParams)
    {
        $rsProps = CIBlockProperty::GetList(array(),array(
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => $arParams['IBLOCK_ID']));

        while ($arProp = $rsProps->Fetch())
        {
            if ($arProp['ID'] === $arParams['ELEMENT_FILTER']) {
                $arParams['ELEMENT_FILTER_NAME'] = $arProp['NAME'];
                break;
            }
        }

        if (isset($_GET["sort"], $_GET['method'])) {
            $arParams["SORT_FIELD"] = $_GET["sort"];
            $arParams["SORT_ORDER"] = $_GET["method"];
        }
        if (!isset($arParams['CACHE_TIME'])) {
            $arParams['CACHE_TIME'] = 360000;
        }
        $arParams['CACHE_TIME'] = (int)$arParams['CACHE_TIME'];
        $arParams['ELEMENTS_COUNT'] = (int)$arParams['ELEMENTS_COUNT'];
        $arParams['DISPLAY_BOTTOM_PAGER'] = true;
        $arParams['PAGER_SHOW_ALWAYS'] = true;
        $arParams['PAGER_TEMPLATE'] = trim($arParams['PAGER_TEMPLATE']);
        $arParams['PAGER_SHOW_ALL'] = false;
        return $arParams;
    }

    public function executeComponent()
    {
        $this->includeModules();
        $this->getResult();
    }

    private function getResult() {
        $arNavParams = array(
            'nPageSize' => $this->arParams['ELEMENTS_COUNT'],
            'bShowAll' => $this->arParams['PAGER_SHOW_ALL']
        );
//        \Bitrix\Main\Diag\Debug::dump($this->getFilterPropName());
        $arNavigation = CDBResult::GetNavParams($arNavParams);
        $arResult = array();
        if ($this->startResultCache($this->arParams['CACHE_TIME'], $arNavigation)) {
            //abortcache
            $arSelect = array('ID', 'PREVIEW_PICTURE', 'NAME', 'PREVIEW_TEXT');
            $arSort = array($this->arParams["SORT_FIELD"] => $this->arParams["SORT_ORDER"]);
            $arFilter = array(
                'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
                'ACTIVE' => 'Y'
            );
            if ($this->checkFilterValue()) {
                $arFilter['!PROPERTY_'.$this->arParams['ELEMENT_FILTER']] = false;
            }
            $res = CIBlockElement::GetList($arSort, $arFilter, false, $arNavParams, $arSelect);
            while ($element = $res->GetNext()) {
                $element["PREVIEW_TEXT"] = strip_tags($element["~PREVIEW_TEXT"]);
                $element["PREVIEW_TEXT"] = mb_substr($element["PREVIEW_TEXT"], 0, 99);
                $element["PREVIEW_PICTURE"] = CFile::GetPath($element["PREVIEW_PICTURE"]);
                $arResult["ITEMS"][] = $element;
            }

            $arResult['NAV_STRING'] = $res->GetPageNavString(
                $this->arParams['PAGER_TITLE'],
                $this->arParams['PAGER_TEMPLATE'],
                $this->arParams['PAGER_SHOW_ALWAYS']
            );
            $this->arResult = $arResult;
            $this->includeComponentTemplate();
        }
    }

    private function checkFilterValue() {
        $arIBlockProp = CIBlockProperty::GetList(array(), array("ACTIVE" => "Y", 'IBLOCK_ID' => $this->arParams['IBLOCK_ID']));
        $filter = $this->request->getQuery('filter');
        while ($res = $arIBlockProp->Fetch()) {
            if ($res['ID'] === $filter && $this->arParams['ELEMENT_FILTER'] === $filter) {
                return true;
            }
        }return false;
    }

    private function getSort() {
        $arSort = array('id', 'name', 'sort');
    }
//    private function getFilterPropName() {
//        $rsProps = CIBlockProperty::GetList(array(),array(
//            'ACTIVE' => 'Y',
//            'IBLOCK_ID' => $this->arParams['IBLOCK_ID']));
//        $result = "";
//        while ($arProp = $rsProps->Fetch())
//        {
//            if ($arProp['ID'] === $this->arParams['ELEMENT_FILTER']) {
//                $result = $arProp['NAME'];
//                break;
//            }
//        }return $result;
//    }

    private function includeModules(): void
    {
        Loader::includeModule('iblock');
    }
}
