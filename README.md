# PHP-ROUTER
构建自己的PHP框架（路由）

Ms.php只有一个文件，去除空行总共也就一百行多一点，通过代码我们能直接看明白它是怎么工作的。下面我简略分析一下：

1.每次 URL 驱动 index.php 之后会在内存中维护一个全量命名空间类名到文件名的数组，这样当我们在代码中使用某个类的时候，将载入该类所在的文件。

2.我们在路由文件中载入了 Ms 类：
```
use core\lib\router\Ms;
```
接着调用了两次静态方法 ::get()，这个方法是不存在的，将由 Ms.php 中的 \__callstatic() 接管。

3.这个函数接受两个参数，$method 和 $params，前者是具体的 function 名称，在这里就是 get，后者是这次调用传递的参数，即
```
Ms::get('/',function(){...}) 
```
中的两个参数。第一个参数是我们想要监听的 URL 值，第二个参数是一个 PHP 闭包，作为回调，代表 URL 匹配成功后我们想要做的事情。

4.\__callstatic() 做的事情也很简单，分别将目标URL（即 /）、HTTP方法（即 GET）和回调代码压入 $routes、$methods 和 $callbacks 三个 Ms 类的静态成员变量（数组）中。

5.路由文件最后一行的
```
Ms::dispatch();
```
方法才是真正处理当前 URL 的地方。能直接匹配到的会直接调用回调，不能直接匹配到的将利用正则进行匹配。

## DEMO
index.php:
```
require 'Ms.php';

use core\lib\router\Ms;

Ms::get('/', 'Controllers\demo@index');
Ms::get('page', 'Controllers\demo@page');
Ms::get('view/(:num)', 'Controllers\demo@view');

Ms::dispatch();
```
demo.php
```
<?php
namespace controllers;

class Demo {

    public function index()
    {
        echo 'home';
    }

    public function page()
    {
        echo 'page';
    }

    public function view($id)
    {
        echo $id;
    }

}

```

.htaccess(Apache):
```
RewriteEngine On
RewriteBase /

# Allow any files or directories that exist to be displayed directly
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

RewriteRule ^(.*)$ index.php?$1 [QSA,L]
```
.htaccess(Nginx):
```
rewrite ^/(.*)/$ /$1 redirect;

if (!-e $request_filename){
	rewrite ^(.*)$ /index.php break;
}
```
