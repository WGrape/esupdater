### 目录
- [1、如何报告问题](#1)
- [2、如何提交PR](#2)
- [3、如何理解项目](#3)
- [4、代码提交规约](#4)
- &nbsp;&nbsp;&nbsp;&nbsp;[1、单测通过](#41)
- &nbsp;&nbsp;&nbsp;&nbsp;[2、commit message 规范](#42)
- &nbsp;&nbsp;&nbsp;&nbsp;[3、分支管理](#43)
- [5、打包Release](#5)
- [6、版本对比](#6)
- [7、项目数据](#7)

## <span id="1">1、如何报告问题</span>
如果在了解和使用过程中，有任何疑问，非常欢迎提出宝贵的 [Issue](https://github.com/WGrape/esupdater/issues/new)

## <span id="2">2、如何提交PR</span>
PR的提交不限制范围，如代码、文档等修改均在允许范围内，可 [参考这里](https://github.com/WGrape/esupdater/commit/186e229308463aa745c6b1cbfd02f77bc62ab9d4) 的PR提交

## <span id="3">3、如何理解项目</span>
在[HOWTOCODE](HOWTOCODE.md)文档中介绍了详细的实现原理和设计，帮助你了解项目

## <span id="4">4、代码提交规约</span>
在提交代码前，至少需要做到以下几项

### <span id="41">(1) 单测通过</span>
整个项目的单元测试必须通过

### <span id="42">(2) commit message 规范</span>
规范使用如```fix: 修复Logger中记录日志时间错误的bug```这种组合的提交规范
- fix: 修复bug相关
- doc: 文档完善相关
- refactor: 重大功能重构
- feat: 新功能、新组件等
- test: 新增测试或测试相关的修改
- style: 调整代码格式等对功能和性能无较大影响的修改
- chore: 构建过程或辅助工具的变动，如dockerfile的修改

### <span id="43">(3) 分支管理</span>
```v1```版本的开发提交到```v1.x```分支，```v2```版本的开发提交到```v2.x```分支，且```CI```检查通过

### <span id="43">(4) 提交内容注释</span>
对于重要代码部分，请以评论的方式写清楚原因，可以参考 [test: 添加环境变量的测试用例](https://github.com/WGrape/esupdater/commit/f9e4b4fe867889f398f3ec175af0d5dfc16de4a0) 、[feat: 支持Composer和修复制作镜像失败时误提示成功的bug](https://github.com/WGrape/esupdater/pull/37/files#r800161416)

## <span id="5">5、打包Release</span>
基于```v1.x```和```v2.x```分支分别打包不同的Release版本。

## <span id="6">6、版本对比</span>

### (1) Composer
| 主版本号 | Composer | 优势 | 劣势 |
| --- | :----:  | :----:  | :----:  |
| v1.x | 不支持 | 不需要安装Composer也可以用 | 可能无法正常使用外部依赖 |
| v2.x | 支持 | 可以方便的调用外部依赖 | 本地开发时需要安装Composer |

## <span id="7">7、项目数据</span>
<a href="https://starchart.cc/WGrape/esupdater"><img src="https://starchart.cc/WGrape/esupdater.svg" width="700"></a>
