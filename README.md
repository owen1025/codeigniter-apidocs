# Codeigniter-apidocs

니가 Codeigniter로 만든 프로젝트의 controller를 분석하여 문서화하며 API를 테스트 해 볼 수 있도록 Request Form과 Response Form을 제공합니다.

## Feature
1. RESTful API 

## Support

1. PHP 5.3 +
2. Codeigniter 2.0 and 3.0
3. IE 8+, Chrome, Safari, Firefox
4. codeigniter-restserver(https://github.com/chriskacerguis/codeigniter-restserver)
5. Return type JSON or XML

## Installation

Download codeigniter-apidocs.zip or git clone. Drag and drop the controller/Docs.php and views/docs.html files into your application's directories. 
Very simply installation!

## Usage

Connect to `base_url/your_project/index.php/docs` or `base_url/your_project/docs`

#### Screenshot

![alt tag](https://cdn.rawgit.com/myartame/codeigniter-apidocs/develop/assets/img/docs_screenshot.png)

#### Description designation method

Your's API Code in 

For example
```php
public function api_test(){
	/*
		@Description This is description 
	*/
	echo "API TEST";
}
```

![alt tag](https://cdn.rawgit.com/myartame/codeigniter-apidocs/develop/assets/img/description_screenshot.png)

## License

MIT License