<?php


namespace DataMapper\QueryBuilder\Statements;

use DataMapper\QueryBuilder\Definitions\DefinitionInterface;

/**
 * Class AlterOption
 * @package DataMapper\QueryBuilder\Statements
 *
 * alter_option: {
 *  table_options
 *  | ADD [COLUMN] col_name column_definition
 *  [FIRST | AFTER col_name]
 *  | ADD [COLUMN] (col_name column_definition,...)
 *  | ADD {INDEX | KEY} [index_name]
 *  [index_type] (key_part,...) [index_option] ...
 *  | ADD {FULLTEXT | SPATIAL} [INDEX | KEY] [index_name]
 *  (key_part,...) [index_option] ...
 *  | ADD [CONSTRAINT [symbol]] PRIMARY KEY
 *  [index_type] (key_part,...)
 *  [index_option] ...
 *  | ADD [CONSTRAINT [symbol]] UNIQUE [INDEX | KEY]
 *  [index_name] [index_type] (key_part,...)
 *  [index_option] ...
 *  | ADD [CONSTRAINT [symbol]] FOREIGN KEY
 *  [index_name] (col_name,...)
 *  reference_definition
 *  | ADD CHECK (expr)
 *  | ALGORITHM [=] {DEFAULT | INPLACE | COPY}
 *  | ALTER [COLUMN] col_name {
 *  SET DEFAULT {literal | (expr)}
 *  | DROP DEFAULT
 * }
 * | CHANGE [COLUMN] old_col_name new_col_name column_definition
 * [FIRST | AFTER col_name]
 * | [DEFAULT] CHARACTER SET [=] charset_name [COLLATE [=] collation_name]
 * | CONVERT TO CHARACTER SET charset_name [COLLATE collation_name]
 * | {DISABLE | ENABLE} KEYS
 * | {DISCARD | IMPORT} TABLESPACE
 * | DROP [COLUMN] col_name
 * | DROP {INDEX | KEY} index_name
 * | DROP PRIMARY KEY
 * | DROP FOREIGN KEY fk_symbol
 * | FORCE
 * | LOCK [=] {DEFAULT | NONE | SHARED | EXCLUSIVE}
 * | MODIFY [COLUMN] col_name column_definition
 * [FIRST | AFTER col_name]
 * | ORDER BY col_name [, col_name] ...
 * | RENAME {INDEX | KEY} old_index_name TO new_index_name
 * | RENAME [TO | AS] new_tbl_name
 * | {WITHOUT | WITH} VALIDATION
 * }
 */
class AlterOption
{
    /** @var string */
    public const ACTION_ADD = 'ADD';

    /** @var string */
    public const ACTION_DROP = 'DROP';

    /** @var string */
    public const ACTION_CHANGE = 'CHANGE';

    /** @var string[] */
    public const ACTIONS = [
        self::ACTION_ADD,
        self::ACTION_DROP,
        self::ACTION_CHANGE,
    ];

//    public function __toString()
//    {
//        // todo: check this logic
//        return '';//return "{$this->action} ";
//    }
//
//    public function init(): string
//    {
//        $alterOption = new AlterAddColumn();
//        return (string)$alterOption;
//    }
}
