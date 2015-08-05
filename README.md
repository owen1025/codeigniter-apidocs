# Codeigniter-apidocs

Codeigniter로 만든 프로젝트의 controller를 분석하여 문서화하며 API를 테스트 해 볼 수 있도록 Request Form과 Response Form을 제공합니다.

## Feature
+ RESTful(GET/POST/PUT/DELETE) 형식으로 API를 Request 할 수 있습니다. 
+ JSON / XML 포맷으로 response를 받아볼 수 있습니다.
+ 파일 업로드를 지원합니다.

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

+ API 코드 내에 `/* @Description <description_content> */` 형식으로 작성하면 API 폼 내에 표기됩니다.

For example
```php
public function api_test(){
	/*
		@Description This is description 
	*/
	echo "API TEST";
}
```


아래 형식으로 보여집니다.
![alt tag](https://cdn.rawgit.com/myartame/codeigniter-apidocs/develop/assets/img/description_screenshot.png)

## License

+ See LICENSE(https://)