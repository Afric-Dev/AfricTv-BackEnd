<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'post_img_path' => 'array',
            'post_img_path.*' => 'nullable|image|max:5120',
            'post_vid_path' => 'nullable|mimes:mp4,avi,mov,wmv,flv',
            // "post_pdf_path" => "nullable|mimes:pdf,doc,docx",
            // "post_song_path" => "nullable|mimes:mp3,wav,aac,flac",
            'category' => 'nullable|string|max:255',
            'post_title' => 'required|string|max:255',
            'PostbodyHtml' => 'required',
            'postbodyJson' => 'nullable',
            'postBodytext' => 'required',
            'link' => 'nullable|url',
            'hashtags' => 'nullable|max:155',
        ];
    }

    /**
     * Get the custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'cover_image.required' => 'The cover image is required.',
            'cover_image.image' => 'The cover image must be an image file.',
            'cover_image.mimes' => 'The cover image must be a file of type: jpeg, png, jpg, gif.',
            'cover_image.max' => 'The cover image must not exceed 5 MB.',
            
            'post_img_path.array' => 'The post images must be an array.',
            'post_img_path.*.required' => 'Each post image is required.',
            'post_img_path.*.image' => 'Each post image must be an image file.',
            'post_img_path.*.max' => 'Each post image must not exceed 5 MB.',
            
            //'post_vid_path.nullable' => 'The video path is optional.',
            'post_vid_path.mimes' => 'The video must be a file of type: mp4, avi, mov, wmv, flv.',
            
            // Uncomment if using these fields
            // 'post_pdf_path.mimes' => 'The PDF must be a file of type: pdf, doc, docx.',
            // 'post_song_path.mimes' => 'The song must be a file of type: mp3, wav, aac, flac.',
            
            // 'category.required' => 'The category field is required.',
            'category.string' => 'The category must be a string.',
            'category.max' => 'The category may not be greater than 255 characters.',
            
            'post_title.required' => 'The post title is required.',
            'post_title.string' => 'The post title must be a string.',
            'post_title.max' => 'The post title may not be greater than 255 characters.',
            
            'PostbodyHtml.required' => 'The HTML body of the post is required.',
            
            // 'postbodyJson.nullable' => 'The JSON body of the post is optional.',
            
            'postBodytext.required' => 'The text body of the post is required.',
            
            //'post_views.nullable' => 'The post views are optional.',
            
            'link.nullable' => 'The link is optional.',
            
            'hashtags.required' => 'The hashtags are required.',
            'hashtags.max' => 'The hashtags may not be greater than 155 characters.',
        ];
    }
}
