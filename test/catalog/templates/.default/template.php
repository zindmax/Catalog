<?
/** @var array $arParams */
/** @var array $arResult */
/** @var array $APPLICATION */

use Bitrix\Main\Diag\Debug;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$this->addExternalCss("https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css");
$this->addExternalJs("https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js");
?>

<?if (!empty($arResult)):?>
<?$APPLICATION->SetTitle("Catalog");?>
<div class="d-flex flex-wrap flex-row justify-content-between">
    <div class="d-flex flex-row w-50">
        <span class="align-middle pe-2">Сортировать: </span>
        <select  class="form-select form-select-sm" name="select-sort" onchange="document.location=this.options[this.selectedIndex].value">
            <?foreach ($arParams["SORT_FIELDS"] as $fieldKey => $field):?>
                <?foreach ($field["method"] as $methodKey => $method):?>
                    <option <?if($method["isSelected"]):?>selected<?endif;?>
                            value="<?=$APPLICATION->GetCurPageParam($method["value"], array("sort", "method", "PAGEN_1"))?>">
                        <?=$field["title"] . " " . GetMessage("SORT_ORDER_".strtoupper($methodKey))?>
                    </option>
                <?endforeach;?>
            <?endforeach;?>
        </select>
    </div>
    <div class="d-flex align-items-center justify-content-between col-sm-4">
        <span>Фильтр: </span>
        <?foreach ($arParams['FILTER_PROPERTY_VALUES'] as $filter):?>
            <a href="<?=$APPLICATION->GetCurPageParam($filter["URL"], array("filter"))?>"><?=$filter["VALUE"]?></a>
        <?endforeach;?>
    </div>
</div>
<div class="d-flex flex-wrap flex-row" >
<?
    foreach ($arResult["ITEMS"] as $arItem):
?>
    <div class="d-flex flex-column w-25">
        <div class="d-flex justify-content-center">
            <img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>"
                 width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>"
                 height="<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>"
                 alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>">
        </div>
        <div class="d-flex justify-content-center text-center">
            <a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem["NAME"]?></a>
        </div>
        <div>
            <p class="mb-0 ps-3"><?=$arItem["PREVIEW_TEXT"]?></p>
        </div>
        <div class="mt-auto">
            <?if($arItem["PROP_VALUES"]):?>
                <p class="mb-0">
                    <?=$arParams["FILTER_PROPERTY"]["NAME"]?>:
                        <?foreach ($arItem["PROP_VALUES"] as $value):?>
                                <?=$value?>
                        <?endforeach;?>
                </p>
            <?endif;?>
        </div>
    </div>
<?endforeach?>
</div>
<?;endif;?>
<br />
<div class="d-flex justify-content-center">
    <?=$arResult["NAV_STRING"]?>
</div>

