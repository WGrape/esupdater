## AutoGenerateCallback
一个自动生成```Handler```和```Service```的事件回调模块的扩展。

### 1、如何使用
- $namespace 参数 ：新增模块的命名空间，如 ```app\alpha\account```
- $moduleName 参数 ：新增的模块名称，如 ```Account```

```shell
php plugin/autogeneratecallback/autogeneratecallback.php {$namespace} {$moduleName}
```

#### 注意事项

- 命名空间中的``` \ ```符号（首位不需要）需要转义，所以别忘记输入两次``` \\ ```，如```app\alpha\account```
- 模块名称使用大驼峰命名，如```MyProfile```

### 2、使用示例
项目中 [account](/app/alpha/account) 模块下的文件即是通过如下命令自动生成而来的。

```shell
 php plugin/autogeneratecallback/autogeneratecallback.php app\\alpha\\account Account
```

<img width="720" alt="img" src="https://user-images.githubusercontent.com/35942268/154846773-73e8bc1b-97e0-4d59-be18-23ebaf123c50.png">

### 3、实现原理
基于 [模板替换](https://www.google.com/search?q=%E6%A8%A1%E6%9D%BF%E6%9B%BF%E6%8D%A2) 原理， 先在模板文件 [handler.template](./handler.template) 和 [service.template](./service.template) 中定义如```{{变量}}```此类的```占位符```，再使用正则匹配把```占位符```替换为目标文本。
