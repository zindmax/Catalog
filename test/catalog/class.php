<?

use Bitrix\Iblock\Component\Tools;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

class CTestCatalog extends CBitrixComponent {

    public function onPrepareComponentParams($arParams): array
    {
        $queryList = $this->request->getQueryList();
        if (isset($queryList["sort"], $queryList["method"]) &&
            ($queryList["sort"] === 'name' || $queryList["sort"] === 'sort')) {
            $arParams["SORT_FIELD"] = $queryList["sort"];
            $arParams["SORT_ORDER"] = $queryList["method"];
        }
        if (!isset($arParams['CACHE_TIME'])) {
            $arParams['CACHE_TIME'] = 360000;
        }
        $arParams['ELEMENT_FILTER_NAME'] = $this->getFilterPropName($arParams);
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

    private function getResult(): void
    {
        $arNavParams = array(
            'nPageSize' => $this->arParams['ELEMENTS_COUNT'],
            'bShowAll' => $this->arParams['PAGER_SHOW_ALL']
        );
        $arNavigation = CDBResult::GetNavParams($arNavParams);
        if ($this->startResultCache($this->arParams['CACHE_TIME'], $arNavigation)) {
            $arSelect = array('ID', 'PREVIEW_PICTURE', 'NAME', 'PREVIEW_TEXT', 'DETAIL_PAGE_URL');
            $arSort = array($this->arParams["SORT_FIELD"] => $this->arParams["SORT_ORDER"]);
            $arFilter = array(
                'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
                'ACTIVE' => 'Y'
            );
            if ($this->checkFilterValue()) {
                $arFilter['!PROPERTY_'.$this->arParams['ELEMENT_FILTER']] = false;
            }
            $res = CIBlockElement::getList($arSort, $arFilter, false, $arNavParams, $arSelect);
            while ($element = $res->GetNext()) {
                Tools::getFieldImageData(
                    $element,
                    array("PREVIEW_PICTURE"),
                    Tools::IPROPERTY_ENTITY_ELEMENT,
                    'IPROPERTY_VALUES'
                );
                $element["PREVIEW_TEXT"] = $this->getPreviewText($element["PREVIEW_TEXT"], $element["PREVIEW_TEXT_TYPE"]);
                $this->arResult["ITEMS"][] = $element;
            }

            $this->arResult['NAV_STRING'] = $res->GetPageNavString(
                $this->arParams['PAGER_TITLE'],
                $this->arParams['PAGER_TEMPLATE'],
                $this->arParams['PAGER_SHOW_ALWAYS'],
                $this
            );
            $this->includeComponentTemplate();
        }
    }

    private function checkFilterValue() :bool
    {
        $arIBlockProp = CIBlockProperty::GetList(array(), array("ACTIVE" => "Y", 'IBLOCK_ID' => $this->arParams['IBLOCK_ID']));
        $filter = $this->request->getQuery('filter');
        while ($res = $arIBlockProp->Fetch()) {
            if ($res['ID'] === $filter && $this->arParams['ELEMENT_FILTER'] === $filter) {
                return true;
            }
        }return false;
    }

    private function getFilterPropName(array $arParams) :string
    {
        $rsProps = CIBlockProperty::GetList(array(),array(
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => $arParams['IBLOCK_ID']));
        $result = "";
        while ($arProp = $rsProps->GetNext())
        {
            if ($arProp['ID'] === $arParams['ELEMENT_FILTER']) {
                $result = $arProp['NAME'];
                break;
            }
        }return $result;
    }

    private function getPreviewText(string $text, string $type): string
    {
        if ($type === "html") {
            $text = HTMLToTxt($text);
        }
        if ($type === "text") {
            $text = str_replace("\xc2\xa0", ' ', html_entity_decode($text));
            $text = strip_tags($text);
        }
        return mb_substr($text, 0,100);
    }

    /** @throws LoaderException */
    private function includeModules(): void
    {
        Loader::includeModule('iblock');
    }
}
