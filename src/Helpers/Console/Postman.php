<?php
namespace HZ\Illuminate\Mongez\Helpers\Console;

use File;
use Illuminate\Support\Str;
use HZ\Illuminate\Mongez\Traits\Console\EngezTrait;

class Postman
{
    use EngezTrait;

    /**
     * Module Name
     *
     * @var string
     */
    protected $moduleName;

    /**
     * singleModuleName
     *
     * @var string
     */
    protected $singleModuleName;

    /**
     * Module data
     *
     * @var array
     */
    protected $data;

    /**
     * Data of PUT and Post form.
     *
     * @var array
     */
    protected $formDataArray = [];

    /**
     * Postman data
     *
     * @var string
     */
    protected $content;

    /**
     * Create postman content.
     *
     * @param array $data
     * @return void
     */
    public function __construct(array $data)
    {
        $this->prepareData($data);
        $this->init();
    }

    /**
     * prepare and set needed data.
     *
     * @param array $data
     * @return void
     */
    protected function prepareData($data)
    {
        $this->data = [];
        $this->singleModuleName = $data['modelName'];
        $this->moduleName = strtolower(str::plural($this->singleModuleName));

        foreach($data['data'] as $textInput => $dataType) {
            $this->data [] = [
                'key'   => $textInput, 
                'type'  => 'text',
                'value' => $dataType
            ];
            
        }

        foreach(explode("," ,$data['uploads']) as $uploadInput) {
            $this->data[] = ['key'=> $uploadInput, 'type'=>'file'];
            
        }

        $this->formDataArray = [
            'POST' => [
                'data' => json_decode(json_encode($this->data), false),
                'type' => "formdata",
            ],
            'PUT' => [
                'data' => json_decode(json_encode($this->data), false),
                'type' => "urlencoded",
            ],
        ];
    }

    /**
     * Init postman file.
     *
     * @return void
     */
    protected function init()
    {
        $content = File::get($this->path("docs/module.postman.json"));

        // replace postman name
        $content = str_ireplace("{postmanName}", $this->moduleName . ' Module', $content);

        // replace module name
        $content = str_ireplace("{moduleName}", $this->moduleName, $content);

        // replace single module name
        $content = str_ireplace("{singleModuleName}", $this->singleModuleName, $content);

        // replace routeUri
        $content = str_ireplace("{routeUri}", $this->moduleName, $content);

        $content = json_decode($content);

        // Set request details
        foreach ($content->item as $item) {
            // set parameters of Add and update request
            if (array_key_exists($item->request->method, $this->formDataArray)) {
                $request = $this->formDataArray[$item->request->method];
                $item->request->body->{$request['type']} = $this->data;
            }
        }

        $this->content = json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Get content of file.
     *
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }
}
