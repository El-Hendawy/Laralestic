<?php

namespace Hendawy\Elasticsearch;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\UrlWindow;

class Pagination extends LengthAwarePaginator
{

    /**
     * Render the paginator using the given view.
     * @param  string  $view
     * @param  array  $data
     * @return string
     */

    public function __construct($items,$itemsTotal, $perPage, $currentPage = null, array $options = [])
    {
        $this->options = $options;

        foreach ($options as $key => $value) {
            $this->{$key} = $value;
        }

        $this->perPage = $perPage;
        $this->currentPage = $this->setCurrentPage($currentPage,$pageNum = 0);
        $this->path = $this->path !== '/' ? rtrim($this->path, '/') : $this->path;
        $this->total = $itemsTotal['value'];

        $this->setItems($items);
    }
    
    protected function setItems($items)
    {
        $this->items = $items instanceof Collection ? $items : Collection::make($items);

        $this->hasMore = $this->items->count() > $this->perPage;

        $this->items = $this->items->slice(0, $this->perPage);
    }
    public function links($view = "default", $data = [])
    {
        extract($data);

        $paginator = $this;

        $elements = $this->elements();

        require dirname(__FILE__) . "/pagination/" . $view . ".php";
    }


    /**
     * Get the array of elements to pass to the view.
     * @return array
     */
    protected function elements()
    {

        $window = UrlWindow::make($this);

        return array_filter([
            $window['first'],
            is_array($window['slider']) ? '...' : null,
            $window['slider'],
            is_array($window['last']) ? '...' : null,
            $window['last'],
        ]);
    }

    /**
     * Determine if the paginator is on the first page.
     * @return bool
     */
    public function onFirstPage()
    {
        return $this->currentPage() <= 1;
    }
}
