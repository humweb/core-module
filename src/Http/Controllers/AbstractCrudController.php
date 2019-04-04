<?php

namespace Humweb\Core\Http\Controllers;

use Illuminate\Http\Request;

class AbstractCrudController extends AdminController
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $modelName = '';

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $views = [];

    /**
     * @var array
     */
    protected $validation = [];

    /**
     * @var array
     */
    protected $validationMessages = [];

    /**
     * @var array
     */
    protected $validationAttributes = [];

    /**
     * @var array
     */
    protected $dispatchable = [];

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * @var string
     */
    protected $redirectRoute = false;

    /**
     * @var int
     */
    protected $recordPerPage = 25;


    /**
     * AbstractCrudController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new $this->modelName;
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getIndex(Request $request)
    {
        $this->callHookIfExists('beforeGetIndex');
        $this->data[$this->name] = $this->model->paginate($this->recordPerPage);
        $this->callHookIfExists('afterGetIndex');

        return $this->setContent($this->views['index'], $this->data);
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getCreate()
    {
        $this->callHookIfExists('beforeGetCreate');

        return $this->setContent($this->views['create'], $this->data);
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getEdit($id)
    {
        $this->callHookIfExists('beforeGetEdit');

        $this->data[str_singular($this->name)] = $this->model->find($id);

        return $this->setContent($this->views['edit'], $this->data);
    }


    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function postCreate(Request $request)
    {
        $this->callHookIfExists('beforePostCreate');

        $this->callValidationIfExists('postCreate', $request);

        if ($this->dispatchable('postCreate')) {
            $model = $this->dispatchAction('postCreate', $request);
        } else {
            $model = $this->model->create($request->all());
        }

        $this->callHookIfExists('afterPostCreate', [$model]);

        // Build redirect from route or redirect back
        $redirect = $this->redirectRoute ? redirect()->route($this->redirectRoute) : back();

        return $redirect->with('success', 'created '.$this->name);
    }


    /**
     * @param \Illuminate\Http\Request $request
     *
     * @param                          $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function postEdit(Request $request, $id)
    {
        $this->callHookIfExists('beforePostEdit');

        $this->callValidationIfExists('postEdit', $request);

        if ($this->dispatchable('postEdit')) {
            $model = $this->dispatchAction('postEdit', $request);
        } else {
            $model = $this->model->find($id);
            $model->fill($request->all());
            $model->save();
        }

        $this->callHookIfExists('afterPostEdit', [$model]);

        // Build redirect from route or redirect back
        $redirect = $this->redirectRoute ? redirect()->route($this->redirectRoute) : back();

        return $redirect->with('success', 'updated '.$this->name);
    }


    /**
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDelete($id)
    {
        $this->callHookIfExists('beforePostDelete');

        if ($this->dispatchable('postDelete')) {
            $model = $this->dispatchAction('postDelete', $request);
        } else {
            $this->model->destroy($id);
        }

        $this->callHookIfExists('afterPostDelete');

        // Build redirect from route or redirect back
        $redirect = $this->redirectRoute ? redirect()->route($this->redirectRoute) : back();

        return $redirect->with('success', 'deleted '.$this->name);
    }


    /**
     * @param $hook
     *
     * @return void
     */
    protected function callHookIfExists($hook, $args = [])
    {
        if (method_exists($this, $hook)) {
            call_user_func_array([$this, $hook], $args);
        }
    }


    /**
     * @param                          $method
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    protected function dispatchAction($method, Request $request)
    {
        return dispatch(new $this->dispatchable[$method]($request));
    }


    /**
     * @param $method
     *
     * @return bool
     */
    protected function dispatchable($method)
    {
        return in_array($method, array_keys($this->dispatchable));
    }


    /**
     * @param $method
     * @param $request
     *
     * @return void
     */
    protected function callValidationIfExists($method, $request)
    {
        if (isset($this->validation[$method])) {
            $this->validate($request, $this->validation[$method], $this->validationMessages, $this->validationAttributes);
        }
    }

}
