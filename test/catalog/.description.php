<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
    "NAME" => GetMessage("COMP_NAME"),
    "DESCRIPTION" => GetMessage("COMP_DESC"),
    "PATH" => array(
        "ID" => "test",
        "NAME" => GetMessage("COMP_PARENT_NAME")
    )
);