<?php

namespace App\Services\Discord;

use Illuminate\Contracts\Support\Arrayable;

enum CommandType: int
{
    case CHAT_INPUT = 1;
    case USER = 2;
    case MESSAGE = 3;
}

enum CommandOptionType: int
{
    case SUB_COMMAND = 1;
    case SUB_COMMAND_GROUP = 2;
    case STRING = 3;
    case INTEGER = 4;
    case BOOLEAN = 5;
    case USER = 6;
    case CHANNEL = 7;
    case ROLE = 8;
    case MENTIONABLE = 9;
    case NUMBER = 10;
    case ATTACHMENT = 11;
}

interface CommandChoiceInterface
{
    protected string $name;
    protected string|int|float $value;
}

interface CommandOptionInterface
{
    protected CommandOptionType $type;
    protected string $name;
    protected string $description;
    protected bool $required = false;
    protected array $choices = [];
    protected array $options = [];
}

abstract class CommandOption implements CommandOptionInterface, Arrayable
{
    public function __construct(
        protected CommandOptionType $type,
        protected string $name,
        protected string $description,
        protected bool $required = false,
    ) {
    }

    public function addOption(CommandOption $option)
    {
        $this->options[] = $option;
    }

    public function addChoice(CommandChoiceInterface $choice)
    {
        $this->choices[] = $choice;
    }

    /**
     * ?????? real.
     *
     * @return array
     */
    public function toArray(): array
    {
        return (array) $this;
    }
}

interface CommandInterface
{
    public string $name;
    public string $description;
    public CommandType $type;
    public CommandOption $options;
}

abstract class Command implements CommandInterface
{
    public function __construct(
        public string $name,
        public string $description,
        public CommandType $type,
    ) {
    }

    public function addOption(CommandOption $option)
    {
        $this->options[] = $option;
    }
}
