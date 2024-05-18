<?php

declare(strict_types=1);

namespace ByteBuddyApi\Value;

final class Command
{
    private function __construct(
        private readonly ?int   $commandId,
        private readonly string $name,
        private readonly string $description,
        private readonly bool   $disabled,
    ) {
    }

    public static function from(int $commandId, string $name, string $descrption, bool $disabled): self
    {
        return new self($commandId, $name, $descrption, $disabled);
    }

    public static function fromDatabase(array $rows): self
    {
        return new self(
            (int)$rows['id'],
            $rows['name'],
            $rows['description'],
            (bool)$rows['disabled']
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            null,
            $data['name'],
            $data['description'],
            $data['disabled']
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->commandId,
            'name' => $this->name,
            'description' => $this->description,
            'disabled' => $this->disabled
        ];
    }

    public function getCommandId(): int
    {
        return $this->commandId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }
}
