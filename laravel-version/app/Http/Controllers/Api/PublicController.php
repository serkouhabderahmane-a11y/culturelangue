<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Traits\ApiResponse;
use App\Models\ContactInfo;
use App\Models\ContactMessage;
use App\Models\Faq;
use App\Models\Lesson;
use App\Models\NavigationItem;
use App\Models\Page;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Setting;
use App\Models\Statistic;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    use ApiResponse;

    public function home()
    {
        $services = Service::where('is_active', true)
            ->with('category')
            ->orderBy('order')
            ->get();

        $categories = ServiceCategory::where('is_active', true)
            ->with(['activeServices'])
            ->orderBy('order')
            ->get();

        $testimonials = Testimonial::where('is_active', true)->orderBy('order')->get();
        $faqs = Faq::where('is_active', true)->orderBy('order')->get();
        $statistics = Statistic::where('is_active', true)->orderBy('order')->get();
        $settings = Setting::pluck('value', 'key')->toArray();

        return $this->success([
            'services' => $services,
            'categories' => $categories,
            'testimonials' => $testimonials,
            'faqs' => $faqs,
            'statistics' => $statistics,
            'settings' => $settings,
        ]);
    }

    public function services()
    {
        $services = Service::where('is_active', true)
            ->with(['category', 'benefits', 'programSessions'])
            ->orderBy('order')
            ->get();

        return $this->success($services);
    }

    public function service(string $slug)
    {
        $service = Service::with(['category', 'benefits', 'programSessions', 'coursePackages', 'groupSchedules'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (! $service) {
            return $this->error('Service not found.', 404);
        }

        return $this->success($service);
    }

    public function categories()
    {
        $categories = ServiceCategory::where('is_active', true)
            ->with(['activeServices'])
            ->orderBy('order')
            ->get();

        return $this->success($categories);
    }

    public function faqs()
    {
        return $this->success(Faq::where('is_active', true)->orderBy('order')->get());
    }

    public function testimonials()
    {
        return $this->success(Testimonial::where('is_active', true)->orderBy('order')->get());
    }

    public function statistics()
    {
        return $this->success(Statistic::where('is_active', true)->orderBy('order')->get());
    }

    public function pages()
    {
        return $this->success(Page::where('is_published', true)->get());
    }

    public function page(string $slug)
    {
        $page = Page::where('slug', $slug)->where('is_published', true)->first();

        if (! $page) {
            return $this->error('Page not found.', 404);
        }

        return $this->success($page);
    }

    public function settings()
    {
        return $this->success(Setting::pluck('value', 'key')->toArray());
    }

    public function navigation()
    {
        return $this->success(
            NavigationItem::orderBy('order')->get()
        );
    }

    public function contactInfo()
    {
        return $this->success(ContactInfo::pluck('value', 'key')->toArray());
    }

    public function lessons()
    {
        $lessons = Lesson::with(['service', 'teacher:id,first_name,last_name'])
            ->where('status', 'scheduled')
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('start_time')
            ->take(50)
            ->get();

        return $this->success($lessons);
    }

    public function contact(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:30',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
            'consent' => 'nullable|boolean',
        ]);

        $message = ContactMessage::create($validated + [
            'status' => 'new',
            'ip_address' => $request->ip(),
        ]);

        return $this->success($message, 'Message sent successfully.', 201);
    }
}
