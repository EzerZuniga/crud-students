<?php
/**
 * Clase para validación de datos
 * Proporciona métodos de validación reutilizables
 */
class Validator
{
    private array $errors = [];
    private array $data = [];

    /**
     * Constructor
     *
     * @param array $data Datos a validar
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Valida que un campo sea requerido
     *
     * @param string $field Nombre del campo
     * @param string $message Mensaje de error personalizado
     * @return self
     */
    public function required(string $field, string $message = null): self
    {
        $value = $this->getValue($field);
        
        if (empty($value) && $value !== '0') {
            $this->errors[$field] = $message ?? "El campo {$field} es obligatorio";
        }

        return $this;
    }

    /**
     * Valida que un campo sea un email válido
     *
     * @param string $field Nombre del campo
     * @param string $message Mensaje de error personalizado
     * @return self
     */
    public function email(string $field, string $message = null): self
    {
        $value = $this->getValue($field);
        
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = $message ?? "El formato del correo es inválido";
        }

        return $this;
    }

    /**
     * Valida la longitud mínima de un campo
     *
     * @param string $field Nombre del campo
     * @param int $min Longitud mínima
     * @param string $message Mensaje de error personalizado
     * @return self
     */
    public function min(string $field, int $min, string $message = null): self
    {
        $value = $this->getValue($field);
        
        if (!empty($value) && mb_strlen($value) < $min) {
            $this->errors[$field] = $message ?? "El campo {$field} debe tener al menos {$min} caracteres";
        }

        return $this;
    }

    /**
     * Valida la longitud máxima de un campo
     *
     * @param string $field Nombre del campo
     * @param int $max Longitud máxima
     * @param string $message Mensaje de error personalizado
     * @return self
     */
    public function max(string $field, int $max, string $message = null): self
    {
        $value = $this->getValue($field);
        
        if (!empty($value) && mb_strlen($value) > $max) {
            $this->errors[$field] = $message ?? "El campo {$field} no debe exceder {$max} caracteres";
        }

        return $this;
    }

    /**
     * Valida que un campo sea numérico
     *
     * @param string $field Nombre del campo
     * @param string $message Mensaje de error personalizado
     * @return self
     */
    public function numeric(string $field, string $message = null): self
    {
        $value = $this->getValue($field);
        
        if (!empty($value) && !is_numeric($value)) {
            $this->errors[$field] = $message ?? "El campo {$field} debe ser numérico";
        }

        return $this;
    }

    /**
     * Valida con una expresión regular personalizada
     *
     * @param string $field Nombre del campo
     * @param string $pattern Patrón regex
     * @param string $message Mensaje de error
     * @return self
     */
    public function pattern(string $field, string $pattern, string $message): self
    {
        $value = $this->getValue($field);
        
        if (!empty($value) && !preg_match($pattern, $value)) {
            $this->errors[$field] = $message;
        }

        return $this;
    }

    /**
     * Verifica si la validación pasó
     *
     * @return bool
     */
    public function passes(): bool
    {
        return empty($this->errors);
    }

    /**
     * Verifica si la validación falló
     *
     * @return bool
     */
    public function fails(): bool
    {
        return !$this->passes();
    }

    /**
     * Obtiene los errores de validación
     *
     * @return array
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Obtiene los datos validados (limpios)
     *
     * @return array
     */
    public function validated(): array
    {
        $validated = [];
        
        foreach ($this->data as $key => $value) {
            $validated[$key] = is_string($value) ? trim($value) : $value;
        }

        return $validated;
    }

    /**
     * Obtiene el valor de un campo
     *
     * @param string $field Nombre del campo
     * @return mixed
     */
    private function getValue(string $field)
    {
        return $this->data[$field] ?? null;
    }

    /**
     * Método estático para crear una instancia y validar
     *
     * @param array $data Datos a validar
     * @param array $rules Reglas de validación
     * @return array [datos_validados, errores]
     */
    public static function make(array $data, array $rules): array
    {
        $validator = new self($data);
        
        foreach ($rules as $field => $fieldRules) {
            foreach ($fieldRules as $rule) {
                if (is_string($rule)) {
                    $validator->$rule($field);
                } elseif (is_array($rule)) {
                    $method = $rule[0];
                    $params = array_slice($rule, 1);
                    $validator->$method($field, ...$params);
                }
            }
        }

        return [$validator->validated(), $validator->errors()];
    }
}
