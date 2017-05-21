<?php declare(strict_types=1);

namespace App\Parser;

/**
 * Class ChainParser
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class ChainParser implements ParserInterface
{
    /** @var ParserInterface[] */
    private $parsers;

    /**
     * ChainParser constructor.
     */
    public function __construct()
    {
        $this->parsers = [
            new ForeachParser(),
            new VariableParser()
        ];
    }

    /**
     * @param string $value
     * @param array  $data
     *
     * @return string
     */
    public function gerParsed(string $value, array $data): string
    {
        foreach ($this->parsers as $parser) {
            $value = $parser->gerParsed($value, $data);
        }

        return $value;
    }
}
