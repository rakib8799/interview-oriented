<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth; // Added for authorization check
use App\Constants\TaskStatus; // Import the constants file
use App\Models\Task;

class TaskStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only authenticated users can create tasks.
        return Auth::check() && $this->user()->can('create', Task::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            // 'status' => ['required', 'string', 'in:' . TaskStatus::PENDING . ',' . TaskStatus::COMPLETED], // Use constants
            'file_path' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx', 'max:2048'], // Added file validation
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'A task title is required.',
            // 'status.in' => 'The status must be either "' . TaskStatus::PENDING . '" or "' . TaskStatus::COMPLETED . '".',
            'file_path.mimes' => 'The uploaded file must be a JPG, JPEG, PNG, PDF, DOC, or DOCX.',
            'file_path.max' => 'The uploaded file must not exceed 2MB.',
        ];
    }
}
