<?
/** @var array $APPLICATION */

use Bitrix\Iblock\Component\Tools;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

class CTestCatalog extends CBitrixComponent {

    public function onPrepareComponentParams($arParams): array
    {
        $arParams["SORT_FIELDS"] = [
            "default" => [
                "title" => "",
                "method" => [
                    "default" => [
                        "isSelected" => true,
                    ]
                ]
            ],
            "name" => [
                "title" => GetMessage("SORT_FIELD_NAME_TITLE"),
                "method" => [
                    "asc" => [
                        "isSelected" => false,
                    ],
                    "desc" => [
                        "isSelected" => false]
            ]],
            "sort" => [
                "title" => GetMessage("SORT_FIELD_S_INDEX_TITLE"),
                "method" => [
                    "asc" => [
                        "isSelected" => false],
                    "desc" => [
                        "isSelected" => false]]
            ]];

        $arParams["SORT_FIELDS"] = $this->createSortMethodValue($arParams["SORT_FIELDS"]);
        $queryList = $this->request->getQueryList();
        if (isset($queryList["sort"], $queryList["method"]) &&
            ($queryList["sort"] === 'name' || $queryList["sort"] === 'sort')) {
            $arParams["SORT_FIELD"] = $queryList["sort"];
            $arParams["SORT_ORDER"] = $queryList["method"];
            $arParams["SORT_FIELDS"]["default"]["method"]["default"]["isSelected"] = false;
            $arParams["SORT_FIELDS"][$arParams["SORT_FIELD"]]["method"][$arParams["SORT_ORDER"]]["isSelected"] = true;
        }
        if (!isset($arParams['CACHE_TIME'])) {
            $arParams['CACHE_TIME'] = 360000;
        }
        $arParams['FILTER_PROPERTY'] = CIBlockProperty::GetByID($arParams["FILTER_PROPERTY_ID"])->Fetch();
        $arParams['FILTER_PROPERTY_VALUES'] = $this->prepareFilterPropValues($arParams);
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

    /**
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\ArgumentException
     */
    private function getResult(): void
    {
        $arNavParams = array(
            'nPageSize' => $this->arParams['ELEMENTS_COUNT'],
            'bShowAll' => $this->arParams['PAGER_SHOW_ALL']
        );
        $arNavigation = CDBResult::GetNavParams($arNavParams);
        $arFilter = array(
            'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
            'ACTIVE' => 'Y',
            'PROPERTY_'.$this->arParams['FILTER_PROPERTY']['CODE'].'_VALUE' => $this->checkFilterValue()
        );
        if ($this->startResultCache(false, [$arNavigation, $arFilter])) {
            $arSelect = array('ID', 'PREVIEW_PICTURE', 'NAME', 'PREVIEW_TEXT', 'DETAIL_PAGE_URL');
            $arSort = array($this->arParams["SORT_FIELD"] => $this->arParams["SORT_ORDER"]);
            $res = CIBlockElement::getList($arSort, $arFilter, false, $arNavParams, $arSelect);
            while ($element = $res->GetNextElement()) {
                $arFields = $element->GetFields();
                if ($arFields["PREVIEW_PICTURE"]["ID"]) {
                    $arFields["PREVIEW_PICTURE"] = $this->preparePreviewImage($arFields);
                }
                $arFields["PREVIEW_TEXT"] = $this->preparePreviewText($arFields["PREVIEW_TEXT"], $arFields["PREVIEW_TEXT_TYPE"]);
                $arFields["PROP_VALUES"] = $element->GetProperty($this->arParams["FILTER_PROPERTY_ID"])["VALUE"];
                $this->arResult["ITEMS"][] = $arFields;
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

    private function createSortMethodValue(array $sortFields)
    {
        foreach ($sortFields as $fieldKey => $field) {
            foreach ($field["method"] as $methodKey => $method) {
                if ($fieldKey === "default") {
                    $sortFields[$fieldKey]["method"][$methodKey]["value"] = "PAGEN_1=1";
                }else{
                    $sortFields[$fieldKey]["method"][$methodKey]["value"] = "sort={$fieldKey}&method={$methodKey}&PAGEN_1=1";
                }
            }
        }
        return $sortFields;
    }

    private function checkFilterValue(): string
    {
        if ($this->request->getQuery('filter') === null) {
            return "";
        }
        $filter = $this->request->getQuery('filter');
        foreach ($this->arParams['FILTER_PROPERTY_VALUES'] as $value) {
            if ($filter === $value["XML_ID"]) {
                return $value["VALUE"];
            }
        }return "";
    }

    private function prepareFilterPropValues(array $arParams): array
    {
        $propValues = [];
        $arProp = CIBlockProperty::GetPropertyEnum(
            $arParams['FILTER_PROPERTY_ID'],
            ["SORT" => "ASC"],
            ["IBLOCK_ID" => $arParams['IBLOCK_ID']]);
        while ($prop = $arProp->Fetch()){
            $propValues[] = $prop;
        }
        foreach ($propValues as $key => $value) {
            $propValues[$key]["URL"] = "filter={$value["XML_ID"]}";
        }
        array_unshift($propValues, ["VALUE" => GetMessage("DEFAULT_FILTER_PROPERTY_VALUE"), "URL" => ""]);
        return $propValues;
    }

    private function preparePreviewImage(array $element): array
    {
        Tools::getFieldImageData(
            $element,
            array("PREVIEW_PICTURE"),
            Tools::IPROPERTY_ENTITY_ELEMENT,
            'IPROPERTY_VALUES'
        );
        $prevPic = $element["PREVIEW_PICTURE"];
        $file = CFile::ResizeImageGet(
            $prevPic["ID"],
            array("width" => 180, "height" => 180),
            BX_RESIZE_IMAGE_EXACT,
            true);
        $prevPic["SRC"] = $file["src"];
        $prevPic["WIDTH"] = $file["width"];
        $prevPic["HEIGHT"] = $file["height"];
        return $prevPic;
    }

    private function preparePreviewText(string $text, string $type): string
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
