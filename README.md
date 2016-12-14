Wefee
==========

Wefee采用[Thinkphp5.3] [2]框架开发。在运行效率，代码质量，程序扩展方面有着市场上其他微信管理系统无法比拟的优势。

Wefee是一套微信公众号运营管理系统，本套系统只提供微信管理的基础功能`关注用户的统计`。对，只有这一个功能，要想实现其他功能，请使用插件的形式丰富。例如：自定义菜单插件。

安装教程
======

### 环境需求
+ php >= 5.5.9 (最好使用php5.6)
+ openssl 拓展
+ rewrite 扩展
+ fileinfo 拓展
+ 命令行可以直接执行php，如：`php -v`

### 1.下载程序。
~~~
Github:
git clone https://github.com/Qsnh/Wefee.git
如果Github访问慢的话，试着使用下面的：
git clone https://git.oschina.net/myteng/Wefee.git
如果您没有安装git的话，请将上面的地址复制并在浏览器打开，直接下载程序文件。
~~~

### 2.安装依赖。
在命令行执行（Windows的CMD）下面代码：
~~~
cd Wefee
php composer.phar install
~~~

### 3.配置数据库信息，并初始化。
数据库信息：
~~~
Windows：
复制.env.example文件并重命名为.env文件，打开配置里面的数据库连接信息
Linux:
cp .env.example .env
vi .env
~~~

安装数据库：
~~~
php think migrate:run
php think seed:run
~~~

### 4.配置Apache目录。
举个例子说明：当前的程序目录为：`/var/www/Wefee`，那么Apache设置的目录应该是`/var/www/Wefee/public`。

### 5.安装完成。
账号：`admin`，密码：`ilovewefee`

### 在这里感谢：
[Easywechat] [1]

[1]: https://easywechat.org/
[2]: http://www.thinkphp.cn/
