<?php
/**
* myPDO是一个用mysql_*()系列函数实现的与PHP自带PDO功能相近的类，它用于把使用PDO的程序快速移植到没有安装PDO但安装了mysql扩展的PHP5空间。
*/
class myPDO {
const PARAM_STR=1;
public static function getAvailableDrivers ( ) {
 }
public function __construct ( $dsn, $username=NULL, $password=NULL, $driver_options ) {
 }
public function beginTransaction ( ) {
 }
public function commit ( ) {
 }
public function errorCode ( ) {
 }
public function errorInfo ( ) {
 }
public function exec ( $statement ) {
 }
public function getAttribute ( $attribute ) {
 }
public function inTransaction ( ) {
 }
public function lastInsertId ( $name=NULL ) {
 }
public function prepare ( $statement, $driver_options=array() ) {
 }
public function query ( $statement ) {
 }
public function quote ( $string, $parameter_type=myPDO::PARAM_STR ) {
 }
public function rollBack ( ) {
 }
public function setAttribute ( $attribute, $value ) {
 }
}