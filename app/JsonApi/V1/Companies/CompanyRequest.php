<?php

namespace App\JsonApi\V1\Companies;

use App\Enums\CompanyStatus;
use App\Enums\DocumentType;
use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;
use LaravelJsonApi\Validation\Rule as JsonApiRule;
use Illuminate\Validation\Rules\Enum;

class CompanyRequest extends ResourceRequest
{
    /**
     * Get the validation rules for the resource.
     *
     * @return array
     */
    public function rules(): array
    {
        $uniqueRule = Rule::unique('companies', 'document_number');
        
        if ($this->isUpdating()) {
            $uniqueRule->ignore($this->model());
        }
        
        return [
            'name' => ['required', 'string', 'max:255'],
            'document_type' => ['required', 'string', new Enum(DocumentType::class)],
            'document_number' => ['required', 'string', 'max:255', $uniqueRule],
            'status' => ['required', 'string', new Enum(CompanyStatus::class)],
            'user' => ['required', JsonApiRule::toOne()],
            'activityTypes' => JsonApiRule::toMany(),
        ];
    }
}