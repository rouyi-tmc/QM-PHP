<?php
/**
 * 分页类
 * 使用方式:
 * $page = new Page();
 * $page->init(1000, 20);
 * $page->setNotActiveTemplate('<span> {a} </span>');
 * $page->setActiveTemplate('{a}');
 * echo $page->show();
 *
 * Created by PhpStorm.
 * User: Rouyi
 * Date: 2017/10/17
 * Time: 下午9:17
 * Email: 383442255@qq.com
 * WebSite: http://qimaweb.com
 */

namespace App\Http\Classes;

class Page
{
    /**
     * 总条数
     *
     * @var int
     */
    private $total;

    /**
     * 每页显示条数
     *
     * @var int
     */
    private $pageSize;

    /**
     * 总页数
     *
     * @var int
     */
    private $pageNum;

    /**
     * 当前页
     *
     * @var int
     */
    private $page;

    /**
     * 分页 URL 地址
     *
     * @var string
     */
    private $uri;

    /**
     * 分页变量
     *
     * @var string
     */
    private $pageParam;

    /**
     * LIMIT XX,XX
     *
     * @var string
     */
    private $limit;

    /**
     * 数字分页显示
     *
     * @var int
     */
    private $listNum = 8;

    /**
     * 分页显示模板
     * 可用变量参数
     * {total}   总数据条数
     * {pageSize}  每页显示条数
     * {start}   本页开始条数
     * {end}    本页结束条数
     * {pageNum}  共有多少页
     * {first}   首页
     * {pre}    上一页
     * {next}    下一页
     * {last}    尾页
     * {list}    数字分页
     * {goTo}    跳转按钮
     *
     * @var string
     */
    private $template = '<nav class="pages" aria-label="Page navigation"><div class="text"><span>共有{total}条</span><span>每页显示{pageSize}条</span>,<span>本页{start}-{end}条</span><span>共有{pageNum}页</span></div><ul class="pagination">{first}{pre}{list}{next}{last}{goTo}</ul></nav>';

    /**
     * 当前选中的分页链接模板
     *
     * @var string
     */
    private $activeTemplate = '<li class="active"><a rel="nofollow" href="javascript:;">{text}</a></li>';

    /**
     * 未选中的分页链接模板
     *
     * @var string
     */
    private $notActiveTemplate = '<li><a href="{url}">{text}</a></li>';

    /**
     * 显示文本设置
     *
     * @var array
     */
    private $config = [
        'first' => '首页',
        'pre' => '&laquo;',
        'next' => '&raquo;',
        'last' => '尾页',
    ];

    /**
     * 初始化
     *
     * @param int $total 总条数
     * @param int $pageSize 每页大小
     * @param string $param url附加参数
     * @param string $pageParam 分页变量
     */
    public function init($total, $pageSize, $param = '', $pageParam = 'page')
    {
        $this->total = intval($total);
        $this->pageSize = intval($pageSize);
        $this->pageParam = $pageParam;
        $this->uri = $this->getUri($param);
        $this->pageNum = ceil($this->total / $this->pageSize);
        $this->page = $this->setPage();
        $this->limit = $this->setLimit();
    }

    /**
     * 设置分页模板
     *
     * @param string $template 模板配置
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * 设置选中分页模板
     *
     * @param string $activeTemplate 模板配置
     */
    public function setActiveTemplate($activeTemplate)
    {
        $this->activeTemplate = $activeTemplate;
    }

    /**
     * 设置未选中分页模板
     *
     * @param string $notActiveTemplate 模板配置
     */
    public function setNotActiveTemplate($notActiveTemplate)
    {
        $this->notActiveTemplate = $notActiveTemplate;
    }

    /**
     * 返回分页
     *
     * @return string
     */
    public function show()
    {
        return str_ireplace(
            [
                '{total}',
                '{pageSize}',
                '{start}',
                '{end}',
                '{pageNum}',
                '{first}',
                '{pre}',
                '{next}',
                '{last}',
                '{list}',
                '{goTo}',
            ],
            [
                $this->total,
                $this->setPageSize(),
                $this->star(),
                $this->end(),
                $this->pageNum,
                $this->first(),
                $this->prev(),
                $this->next(),
                $this->last(),
                $this->pageList(),
                $this->goPage(),
            ],
            $this->template
        );
    }

    /**
     * 获取limit起始数
     *
     * @return int
     */
    public function getOffset()
    {
        return ($this->page - 1) * $this->pageSize;
    }

    /**
     * 设置LIMIT
     *
     * @return string
     */
    private function setLimit()
    {
        return "limit " . ($this->page - 1) * $this->pageSize . ",{$this->pageSize}";
    }

    /**
     * 获取LIMIT
     *
     * @param string $args
     * @return string|null
     */
    public function __get($args)
    {
        if ($args == "limit") {
            return $this->limit;
        }

        return null;
    }

    /**
     * 初始化当前页
     *
     * @return int
     */
    private function setPage()
    {
        if (!empty($_GET[$this->pageParam])) {
            if ($_GET[$this->pageParam] > 0) {
                if ($_GET[$this->pageParam] > $this->pageNum) {
                    return $this->pageNum;
                } else {
                    return $_GET[$this->pageParam];
                }
            }
        }

        return 1;
    }

    /**
     * 初始化url
     *
     * @param string $param
     * @return string
     */
    private function getUri($param)
    {
        $url = $_SERVER['REQUEST_URI'] . (strpos($_SERVER['REQUEST_URI'], "?") ? '' : "?") . $param;
        $parse = parse_url($url);
        if (isset($parse["query"])) {
            parse_str($parse["query"], $params);
            unset($params["page"]);
            $url = $parse["path"] . "?" . http_build_query($params);
            return $url;
        }

        return $url;
    }

    /**
     * 本页开始条数
     *
     * @return int
     */
    private function star()
    {
        if ($this->total == 0) {
            return 0;
        }

        return ($this->page - 1) * $this->pageSize + 1;
    }

    /**
     * 本页结束条数
     *
     * @return int
     */
    private function end()
    {
        return min($this->page * $this->pageSize, $this->total);
    }

    /**
     * 设置当前页大小
     *
     * @return int
     */
    private function setPageSize()
    {
        return $this->end() - $this->star() + 1;
    }

    /**
     * 首页
     *
     * @return string
     */
    private function first()
    {
        $html = '';
        if ($this->page == 1) {
            $html .= $this->replace("{$this->uri}&page=1", $this->config['first'], true);
        } else {
            $html .= $this->replace("{$this->uri}&page=1", $this->config['first'], false);
        }

        return $html;
    }

    /**
     * 上一页
     *
     * @return string
     */
    private function prev()
    {
        $html = '';
        if ($this->page > 1) {
            $html .= $this->replace($this->uri . '&page=' . ($this->page - 1), $this->config['pre'], false);
        } else {
            $html .= $this->replace($this->uri . '&page=' . ($this->page - 1), $this->config['pre'], true);
        }

        return $html;
    }

    /**
     * 分页数字列表
     *
     * @return string
     */
    private function pageList()
    {
        $linkPage = '';
        $lastList = floor($this->listNum / 2);
        for ($i = $lastList; $i >= 1; $i--) {
            $page = $this->page - $i;
            if ($page < 1) continue;

            $linkPage .= $this->replace("{$this->uri}&page={$page}", $page, false);
        }

        $linkPage .= $this->replace("{$this->uri}&page={$this->page}", $this->page, true);
        for ($i = 1; $i <= $lastList; $i++) {
            $page = $this->page + $i;
            if ($page < $this->pageNum) break;

            $linkPage .= $this->replace("{$this->uri}&page={$page}", $page, false);
        }

        return $linkPage;
    }

    /**
     * 下一页
     *
     * @return string
     */
    private function next()
    {
        $html = '';
        if ($this->page < $this->pageNum) {
            $html .= $this->replace($this->uri . '&page=' . ($this->page + 1), $this->config['next'], false);
        } else {
            $html .= $this->replace($this->uri . '&page=' . ($this->page + 1), $this->config['next'], true);
        }

        return $html;
    }

    /**
     * 最后一页
     *
     * @return string
     */
    private function last()
    {
        $html = '';
        if ($this->page == $this->pageNum) {
            $html .= $this->replace($this->uri . '&page=' . ($this->pageNum), $this->config['last'], true);
        } else {
            $html .= $this->replace($this->uri . '&page=' . ($this->pageNum), $this->config['last'], false);
        }

        return $html;
    }

    /**
     * 跳转按钮
     *
     * @return string
     */
    private function goPage()
    {
        $html = '';
        $html .= '<li class="goPage"><div class="input-group"><input type="text" value="' . $this->page . '" onkeydown="javascript:if(event.keyCode==13){var page=(this.value>' . $this->pageNum . ')?' . $this->pageNum . ':this.value;location=\'' . $this->uri . '&page=\'+page+\'\'}" class="form-control" aria-describedby="basic-addon2"><span class="input-group-addon btn" id="basic-addon2" onclick="javascript:var page=(this.previousSibling.value>' . $this->pageNum . ')?' . $this->pageNum . ':this.previousSibling.value;location=\'' . $this->uri . '&page=\'+page+\'\'">GO</span></div></li>';
        return $html;
    }

    /**
     * 模板替换
     *
     * @param string $url
     * @param string $text
     * @param bool $result
     * @return string
     */
    private function replace($url, $text, $result = true)
    {
        $template = ($result ? $this->activeTemplate : $this->notActiveTemplate);
        $html = str_replace('{url}', $url, $template);
        $html = str_replace('{text}', $text, $html);
        return $html;
    }
}