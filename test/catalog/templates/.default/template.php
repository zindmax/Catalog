<?
/** @var array $arParams */
/** @var array $arResult */
/** @var array $APPLICATION */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$this->addExternalCss("https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css");
$this->addExternalJs("https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js");
?>

<?if (!empty($arResult)):?>
<div class="d-flex flex-wrap flex-row justify-content-between">
    <div>
        <span>Сортировать по: </span>
        <select name="select-sort" onchange="document.location=this.options[this.selectedIndex].value">
            <option <?if(empty($_REQUEST['sort'])):?>selected<?endif;?> value="<?=$APPLICATION->GetCurPageParam("", array("sort", "method"))?>">Без сортировки</option>
            <option <?if($_REQUEST['sort'] === 'name' && $_REQUEST['method'] === 'asc'):?>selected<?endif;?>
                    value="<?=$APPLICATION->GetCurPageParam("sort=name&method=asc", array("sort", "method"));?>">имени по возрастанию</option>
            <option <?if($_REQUEST['sort'] === 'name' && $_REQUEST['method'] === 'desc'):?>selected<?endif;?>
                    value="<?=$APPLICATION->GetCurPageParam("sort=name&method=desc", array("sort", "method"));?>">имени по убыванию</option>
            <option <?if($_REQUEST['sort'] === 'sort' && $_REQUEST['method'] === 'asc'):?>selected<?endif;?>
                    value="<?=$APPLICATION->GetCurPageParam("sort=sort&method=asc", array("sort", "method"));?>">По индексу по возрастанию</option>
            <option <?if($_REQUEST['sort'] === 'sort' && $_REQUEST['method'] === 'desc'):?>selected<?endif;?>
                    value="<?=$APPLICATION->GetCurPageParam("sort=sort&method=desc", array("sort", "method"));?>">По индексу по возрастанию</option>
        </select>
    </div>
    <div>
        <span>Фильтр</span>
        <a href="<?=$APPLICATION->GetCurPageParam("", array("filter"))?>">Все</a>
        <a href="<?=$APPLICATION->GetCurPageParam("filter={$arParams['ELEMENT_FILTER']}", array("filter"))?>"><?=$arParams['ELEMENT_FILTER_NAME']?></a>
    </div>
</div>
<div class="d-flex flex-wrap flex-row" >
<?
    foreach ($arResult["ITEMS"] as $arItem):
?>
<div class="w-25">
    <img src="<?=$arItem["PREVIEW_PICTURE"]?>" alt="<?=$arItem["NAME"]?>">
    <a href=""><?=$arItem["NAME"]?></a>
    <div>
        <?=$arItem["PREVIEW_TEXT"]?>
    </div>
</div>
<?endforeach?>
</div>
<?;endif;?>
<br /> <?=$arResult["NAV_STRING"]?>
