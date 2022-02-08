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
            <option <? if(empty($_REQUEST['sort'])):?>selected<?endif;?> value="<?=$APPLICATION->GetCurPageParam("", array("sort", "method"))?>">Без сортировки</option>
            <?foreach ($arParams["SORT_FIELDS"] as $key => $field):?>
                <option <?if($arParams["SORT_FIELD"] === $key && $arParams["SORT_ORDER"] === 'asc'):?>selected<?endif;?>
                    value="<?=$APPLICATION->GetCurPageParam("sort={$key}&method=asc", array("sort", "method"))?>">
                    <?=$field["title"] . " " . GetMessage("SORT_ORDER_ASC")?>
                </option>
                <option <?if($arParams["SORT_FIELD"] === $key && $arParams["SORT_ORDER"] === 'desc'):?>selected<?endif;?>
                    value="<?=$APPLICATION->GetCurPageParam("sort={$key}&method=desc", array("sort", "method"))?>">
                    <?=$field["title"] . " " . GetMessage("SORT_ORDER_DESC")?>
                </option>
            <?endforeach;?>
        </select>
    </div>
    <div class="d-flex align-items-center justify-content-between w-25">
        <span>Фильтр: </span>
        <a href="<?=$APPLICATION->GetCurPageParam("", array("filter"))?>">Все</a>
        <?foreach ($arParams['FILTER_VALUES'] as $filter):?>
            <a href="<?=$APPLICATION->GetCurPageParam("filter={$filter["XML_ID"]}", array("filter"))?>"><?=$filter["VALUE"]?></a>
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
    </div>
<?endforeach?>
</div>
<?;endif;?>
<br />
<div class="d-flex justify-content-center">
    <?=$arResult["NAV_STRING"]?>
</div>

