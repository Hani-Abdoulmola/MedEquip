<?php

namespace App\Http\Controllers\Web\Buyers;

use App\Http\Controllers\Controller;
use App\Models\Rfq;
use App\Models\RfqTemplate;
use App\Models\RfqTemplateItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class BuyerRfqTemplateController extends Controller
{
    /**
     * Display list of RFQ templates.
     */
    public function index(Request $request): View
    {
        $buyer = Auth::user()->buyerProfile;

        $query = RfqTemplate::with('items')
            ->where('buyer_id', $buyer->id)
            ->latest('last_used_at');

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $templates = $query->paginate(12)->withQueryString();

        return view('buyer.rfq-templates.index', compact('templates'));
    }

    /**
     * Show template details.
     */
    public function show(RfqTemplate $template): View
    {
        if ($template->buyer_id !== Auth::user()->buyerProfile->id) {
            abort(403, 'ليس لديك صلاحية لعرض هذا القالب');
        }

        $template->load('items.product');

        return view('buyer.rfq-templates.show', compact('template'));
    }

    /**
     * Create RFQ from template.
     */
    public function use(RfqTemplate $template): RedirectResponse
    {
        $buyer = Auth::user()->buyerProfile;

        if ($template->buyer_id !== $buyer->id) {
            abort(403, 'ليس لديك صلاحية لاستخدام هذا القالب');
        }

        try {
            $rfq = $template->createRfq();

            return redirect()
                ->route('buyer.rfqs.edit', $rfq)
                ->with('success', 'تم إنشاء طلب عرض سعر من القالب بنجاح. يمكنك الآن مراجعة وتعديل الطلب.');

        } catch (\Exception $e) {
            Log::error('Template usage error', [
                'template_id' => $template->id,
                'buyer_id' => $buyer->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'حدث خطأ أثناء إنشاء الطلب من القالب']);
        }
    }

    /**
     * Save RFQ as template.
     */
    public function saveFromRfq(Request $request, Rfq $rfq): RedirectResponse
    {
        $buyer = Auth::user()->buyerProfile;

        if ($rfq->buyer_id !== $buyer->id) {
            abort(403, 'ليس لديك صلاحية لحفظ هذا الطلب كقالب');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|in:general,emergency,recurring,department,project,custom',
            'department' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // Create template
            $template = RfqTemplate::create([
                'buyer_id' => $buyer->id,
                'name' => $validated['name'],
                'description' => $validated['description'] ?? $rfq->description,
                'category' => $validated['category'],
                'department' => $validated['department'] ?? null,
                'default_deadline_days' => $rfq->deadline ? now()->diffInDays($rfq->deadline) : 7,
                'is_public' => $rfq->is_public,
            ]);

            // Copy RFQ items to template
            $rfq->load('items');
            foreach ($rfq->items as $index => $item) {
                RfqTemplateItem::create([
                    'template_id' => $template->id,
                    'product_id' => $item->product_id,
                    'item_name' => $item->item_name,
                    'specifications' => $item->specifications,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                    'sort_order' => $index,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('buyer.rfq-templates.show', $template)
                ->with('success', 'تم حفظ القالب بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Save template error', [
                'rfq_id' => $rfq->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'حدث خطأ أثناء حفظ القالب']);
        }
    }

    /**
     * Delete template.
     */
    public function destroy(RfqTemplate $template): RedirectResponse
    {
        $buyer = Auth::user()->buyerProfile;

        if ($template->buyer_id !== $buyer->id) {
            abort(403, 'ليس لديك صلاحية لحذف هذا القالب');
        }

        $template->delete();

        return redirect()
            ->route('buyer.rfq-templates.index')
            ->with('success', 'تم حذف القالب بنجاح');
    }
}

