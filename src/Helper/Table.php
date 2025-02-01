<?php
/**
 * Created by chen3jian
 * Date: 2021/8/4
 * Time: 19:55
 */

namespace jz\Helper;


/**
 * Created by chen3jian
 * Date: 2021/8/4
 * Time: 19:55
 * Class Table
 * @package jz\Helper
 */
class Table
{
    /** @var int 左对齐 */
    const ALIGN_LEFT = 1;

    /** @var int 右对齐 */
    const ALIGN_RIGHT = 0;

    /** @var int 居中对齐 */
    const ALIGN_CENTER = 2;

    /** @var array 头信息数据 */
    protected $header = [];

    /** @var int 头部对齐方式 默认1 ALGIN_LEFT 0 ALIGN_RIGHT 2 ALIGN_CENTER */
    protected $headerAlign = self::ALIGN_LEFT;

    /** @var array 表格数据（二维数组） */
    protected $rows = [];

    /** @var int 单元格对齐方式 默认1 ALGIN_LEFT 0 ALIGN_RIGHT 2 ALIGN_CENTER */
    protected $cellAlign = self::ALIGN_LEFT;

    /** @var array 单元格宽度信息 */
    protected $colWidth = [];

    /** @var string 表格输出样式 */
    protected $style = 'default';

    /** @var array 表格样式定义 */
    protected $format = [
        'compact' => [],
        'default' => [
            'top' => ['+', '-', '+', '+'],
            'cell' => ['|', ' ', '|', '|'],
            'middle' => ['+', '-', '+', '+'],
            'bottom' => ['+', '-', '+', '+'],
            'cross-top' => ['+', '-', '-', '+'],
            'cross-bottom' => ['+', '-', '-', '+'],
        ],
        'markdown' => [
            'top' => [' ', ' ', ' ', ' '],
            'cell' => ['|', ' ', '|', '|'],
            'middle' => ['|', '-', '|', '|'],
            'bottom' => [' ', ' ', ' ', ' '],
            'cross-top' => ['|', ' ', ' ', '|'],
            'cross-bottom' => ['|', ' ', ' ', '|'],
        ],
        'borderless' => [
            'top' => ['=', '=', ' ', '='],
            'cell' => [' ', ' ', ' ', ' '],
            'middle' => ['=', '=', ' ', '='],
            'bottom' => ['=', '=', ' ', '='],
            'cross-top' => ['=', '=', ' ', '='],
            'cross-bottom' => ['=', '=', ' ', '='],
        ],
        'box' => [
            'top' => ['┌', '─', '┬', '┐'],
            'cell' => ['│', ' ', '│', '│'],
            'middle' => ['├', '─', '┼', '┤'],
            'bottom' => ['└', '─', '┴', '┘'],
            'cross-top' => ['├', '─', '┴', '┤'],
            'cross-bottom' => ['├', '─', '┬', '┤'],
        ],
        'box-double' => [
            'top' => ['╔', '═', '╤', '╗'],
            'cell' => ['║', ' ', '│', '║'],
            'middle' => ['╠', '─', '╪', '╣'],
            'bottom' => ['╚', '═', '╧', '╝'],
            'cross-top' => ['╠', '═', '╧', '╣'],
            'cross-bottom' => ['╠', '═', '╤', '╣'],
        ],
    ];

    /**
     * 设置表格头信息 以及对齐方式
     * @param array $header 要输出的Header信息
     * @param int $align 对齐方式 默认1 ALGIN_LEFT 0 ALIGN_RIGHT 2 ALIGN_CENTER
     * @return void
     * @Time：2025/2/2 02:08:14
     * @Since：v2.0
     * @author：cxj
     */
    public function setHeader(array $header, int $align = self::ALIGN_LEFT)
    {
        $this->header = $header;
        $this->headerAlign = $align;
        $this->checkColWidth($header);
    }

    /**
     * 设置输出表格数据 及对齐方式
     * @param array $rows 要输出的表格数据（二维数组）
     * @param int $align 对齐方式 默认1 ALGIN_LEFT 0 ALIGN_RIGHT 2 ALIGN_CENTER
     * @return void
     * @Time：2025/2/2 02:08:57
     * @Since：v2.0
     * @author：cxj
     */
    public function setRows(array $rows, int $align = self::ALIGN_LEFT)
    {
        $this->rows = $rows;
        $this->cellAlign = $align;

        foreach ($rows as $row) {
            $this->checkColWidth($row);
        }
    }

    /**
     * 检查列数据的显示宽度
     * @param $row
     * @return void
     * @Time：2025/2/2 02:09:56
     * @Since：v2.0
     * @author：cxj
     */
    protected function checkColWidth($row)
    {
        if (is_array($row)) {
            foreach ($row as $key => $cell) {
                if (!isset($this->colWidth[$key]) || strlen($cell) > $this->colWidth[$key]) {
                    $this->colWidth[$key] = strlen($cell);
                }
            }
        }
    }

    /**
     * 增加一行表格数据
     * @param $row
     * @param bool $first 是否在开头插入
     * @return void
     * @Time：2025/2/2 02:10:41
     * @Since：v2.0
     * @author：cxj
     */
    public function addRow($row, bool $first = false)
    {
        if ($first) {
            array_unshift($this->rows, $row);
        } else {
            $this->rows[] = $row;
        }

        $this->checkColWidth($row);
    }

    /**
     * 设置输出表格的样式
     * @param string $style 样式名
     * @return void
     * @Time：2025/2/2 02:11:14
     * @Since：v2.0
     * @author：cxj
     */
    public function setStyle(string $style)
    {
        $this->style = isset($this->format[$style]) ? $style : 'default';
    }

    /**
     * 输出分隔行
     * @param string $pos
     * @return string
     * @Time：2025/2/2 03:50:32
     * @Since：v2.0
     * @author：cxj
     */
    protected function renderSeparator(string $pos): string
    {
        $style = $this->getStyle($pos);
        $array = [];

        foreach ($this->colWidth as $width) {
            $array[] = str_repeat($style[1], $width + 2);
        }

        return $style[0] . implode($style[2], $array) . $style[3] . PHP_EOL;
    }

    /**
     * 输出表格头部
     * @return string
     * @Time：2025/2/2 03:50:44
     * @Since：v2.0
     * @author：cxj
     */
    protected function renderHeader(): string
    {
        $style = $this->getStyle('cell');
        $content = $this->renderSeparator('top');

        foreach ($this->header as $key => $header) {
            $array[] = ' ' . str_pad($header, $this->colWidth[$key], $style[1], $this->headerAlign);
        }

        if (!empty($array)) {
            $content .= $style[0] . implode(' ' . $style[2], $array) . ' ' . $style[3] . PHP_EOL;

            if ($this->rows) {
                $content .= $this->renderSeparator('middle');
            }
        }

        return $content;
    }

    /**
     * 获取风格
     * @param string $style
     * @return string[]
     * @Time：2025/2/2 03:50:52
     * @Since：v2.0
     * @author：cxj
     */
    protected function getStyle(string $style): array
    {
        if ($this->format[$this->style]) {
            $style = $this->format[$this->style][$style];
        } else {
            $style = [' ', ' ', ' ', ' '];
        }

        return $style;
    }

    /**
     * 输出表格
     * @param array $dataList
     * @return string
     * @Time：2025/2/2 03:51:01
     * @Since：v2.0
     * @author：cxj
     */
    public function render(array $dataList = []): string
    {
        if ($dataList) {
            $this->setRows($dataList);
        }

        // 输出头部
        $content = $this->renderHeader();
        $style = $this->getStyle('cell');

        if ($this->rows) {
            foreach ($this->rows as $row) {
                if (is_string($row) && '-' === $row) {
                    $content .= $this->renderSeparator('middle');
                } elseif (is_scalar($row)) {
                    $content .= $this->renderSeparator('cross-top');
                    $array = str_pad($row, 3 * (count($this->colWidth) - 1) + array_reduce($this->colWidth, function ($a, $b) {
                            return $a + $b;
                        }));

                    $content .= $style[0] . ' ' . $array . ' ' . $style[3] . PHP_EOL;
                    $content .= $this->renderSeparator('cross-bottom');
                } else {
                    $array = [];

                    foreach ($row as $key => $val) {
                        $array[] = ' ' . str_pad($val, $this->colWidth[$key], ' ', $this->cellAlign);
                    }

                    $content .= $style[0] . implode(' ' . $style[2], $array) . ' ' . $style[3] . PHP_EOL;

                }
            }
        }
        $content .= $this->renderSeparator('bottom');
        return $content;
    }
}
