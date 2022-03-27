# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## 2.4.1 - 2022-03-26

* Fix php 8.1 deprecation notice for ord(null)

## 2.4.0 - 2018-12-06

* Preserve ES6 template literals

## 2.3.2 - 2015-03-30

* Correctly identifies regexes following keywords with no space. E.g. return/regex/;

## 2.3.1 - 2014-08-25

* Exception classes are PSR-0 loadable

## 2.3.0 - 2014-08-25

Rework as JSMin library on packagist.
Releases prior this version are contained in minify package.

* Removes leading UTF-8 BOM

## 2.2.0

* Fix handling of RegEx in certain situations in JSMin
* Fix bug in JSMin exceptions

## 2.1.6

* JSMin fixes

## 2.1.4

* JSMin won't choke on common Closure compiler syntaxes (i+ ++j)
* mbstring.func_overload usage is safer

## 2.1.2

* quote characters inside RegExp literals no longer cause exception

## 2.1.0

* JS: preserves IE conditional comments

## 1.0.1 - 2007-05-05

* Replaced old JSMin library with a much faster custom implementation.
