# Codeigniter-apidocs

Parsing Codeigniter project's controller and make api documents. Also provide Request Form and Response Form for api testing.

## Feature
+ Restful api request support 
+ JSON/ XML response format support

## Support

+ PHP 5.3 +
+ Codeigniter 2.0 or 3.0
+ IE 8+, Chrome, Safari, Firefox
+ codeigniter-restserver(https://github.com/chriskacerguis/codeigniter-restserver)

## Installation

Download codeigniter-apidocs.zip or git clone. Drag and drop the controller/Docs.php and views/docs.html files into your application's directories. 
Very simply installation!

## Usage

Connect to `base_url/your_project/index.php/docs` or `base_url/your_project/docs`

#### Screenshot

![alt tag](https://cdn.rawgit.com/myartame/codeigniter-apidocs/develop/assets/img/docs_screenshot.png)

#### Description designation method

+ Once insert `/* @Description <description_content> */` at api code, Api form will apper. 

For example
```php
public function api_test(){
	/*
		@Description This is description 
	*/
	echo "API TEST";
}
```


will show below

![alt tag](https://cdn.rawgit.com/myartame/codeigniter-apidocs/develop/assets/img/description_screenshot.png)

## License

+ See [LICENSE](https://https://cdn.rawgit.com/myartame/codeigniter-apidocs/master/LICENSE)
