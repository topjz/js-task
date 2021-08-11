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
    /**
     * 左对齐
     */
    const ALIGN_LEFT = 1;

    /**
     * 右对齐
     */
    const ALIGN_RIGHT = 0;

    /**
     * 居中对齐
     */
    const ALIGN_CENTER = 2;

    /**
     * 头信息数据
     * @var array
     */
    protected $header = [];

    /**
     * 头部对齐方式 默认1 ALGIN_LEFT 0 ALIGN_RIGHT 2 ALIGN_CENTER
     * @var int
     */
    protected $headerAlign = self::ALIGN_LEFT;

    /**
     * 表格数据（二维数组）
     * @var array
     */
    protected $rows = [];

    /**
     * 单元格对齐方式 默认1 ALGIN_LEFT 0 ALIGN_RIGHT 2 ALIGN_CENTER
     * @var int
     */
    protected $cellAlign = self::ALIGN_LEFT;

    /**
     * 单元格宽度信息
     * @var array
     */
    protected $colWidth = [];

    /**
     * 表格输出样式
     * @var string
     */
    protected $style = 'default';

    /**
     * 表格样式定义
     * @var array
     */
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
     */

    /**
     * 设置表格头信息 以及对齐方式
     * @param array $header 要输出的Header信息
     * @param int $align 对齐方式 默认1 ALGIN_LEFT 0 ALIGN_RIGHT 2 ALIGN_CENTER
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/8/4 19:58
     */
    public function setHeader(array $header, $align = self::ALIGN_LEFT)
    {
        $this->header = $header;
        $this->headerAlign = $align;
        $this->checkColWidth($header);
    }

    /**
     * 设置输出表格数据 及对齐方式
     * @param array $rows 要输出的表格数据（二维数组）
     * @param int $align 对齐方式 默认1 ALGIN_LEFT 0 ALIGN_RIGHT 2 ALIGN_CENTER
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/8/4 19:58
     */
    public function setRows(array $rows, $align = self::ALIGN_LEFT)
    {
        $this->rows = $rows;
        $this->cellAlign = $align;

        foreach ($rows as $row) {
            $this->checkColWidth($row);
        }
    }

    /**
     * 检查列数据的显示宽度
     * @param mixed $row 行数据
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/8/4 19:59
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
     * @param mixed $row 行数据
     * @param bool $first 是否在开头插入
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/8/4 19:59
     */
    public function addRow($row, $first = false)
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
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/8/4 19:59
     */
    public function setStyle(string $style)
    {
        $this->style = isset($this->format[$style]) ? $style : 'default';
    }

    /**
     * 输出分隔行
     * @param string $pos 位置
     * @return string
     *
     */

    /**
     * 输出分隔行
     * @param string $pos 位置
     * @return string
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/8/4 19:59
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
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/8/4 19:59
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
     * @return array
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/8/4 19:59
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
     * @param array $dataList 表格数据
     * @return string
     * @author：cxj
     * @since：v1.0
     * @Time: 2021/8/4 19:59
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