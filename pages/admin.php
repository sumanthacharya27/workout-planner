<!-- Admin Templates Page -->
<section id="adminPage" class="page hidden">
    <div class="max-w-5xl mx-auto space-y-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Admin Panel</h2>
            <p class="text-sm text-gray-600">Manage pre-made workout templates.</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Add New Template</h3>
            <form id="adminTemplateForm" class="space-y-4">
                <div>
                    <label for="adminTemplateName" class="block text-sm font-medium text-gray-700 mb-1">Template Name</label>
                    <input id="adminTemplateName" type="text" maxlength="150" required class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="e.g. Beginner Full Body A">
                </div>
                <div>
                    <label for="adminTemplateDescription" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="adminTemplateDescription" rows="4" maxlength="2000" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Write a short template description"></textarea>
                </div>
                <button type="submit" class="inline-flex items-center justify-center rounded-md bg-indigo-600 text-white px-4 py-2 text-sm font-semibold hover:bg-indigo-700">Add Template</button>
                <p id="adminTemplateMessage" class="text-sm"></p>
            </form>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Existing Templates</h3>
            <div id="adminTemplatesList" class="space-y-3"></div>
        </div>
    </div>
</section>
