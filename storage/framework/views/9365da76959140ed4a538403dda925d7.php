

<?php $__env->startSection('title', __('Report an Issue')); ?>

<?php $__env->startSection('dashboard_content'); ?>
<section class="auth-card" aria-label="<?php echo e(__('Report an Issue')); ?>">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <h1 style="margin:0;"><?php echo e(__('Report an Issue')); ?></h1>
        <a href="<?php echo e(route('tickets.index')); ?>" class="btn btn-outline"><?php echo e(__('View My Tickets')); ?></a>
    </div>

    <?php if($errors->any()): ?>
        <div role="alert" style="margin-top:12px; padding:10px 12px; border:1px solid var(--c-border); border-radius:10px; background: color-mix(in srgb, var(--c-surface) 92%, #ef4444 8%);">
            <ul style="margin:0; padding-left:20px;">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('tickets.store')); ?>" style="margin-top:24px;">
        <?php echo csrf_field(); ?>

        <div style="display:grid; gap:20px;">
            <div>
                <label for="error_type" style="display:block; margin-bottom:6px; font-weight:600;"><?php echo e(__('Error Type')); ?></label>
                <select id="error_type" name="error_type" required style="width:100%; padding:10px 12px; border:1px solid var(--c-border); border-radius:8px; background:var(--c-surface); color:var(--c-on-surface);">
                    <option value=""><?php echo e(__('Select the type of error...')); ?></option>
                    <option value="simulation_error" <?php echo e(old('error_type') === 'simulation_error' ? 'selected' : ''); ?>><?php echo e(__('Simulation Error')); ?></option>
                    <option value="visual_error" <?php echo e(old('error_type') === 'visual_error' ? 'selected' : ''); ?>><?php echo e(__('Visual/UI Error')); ?></option>
                    <option value="personal_error" <?php echo e(old('error_type') === 'personal_error' ? 'selected' : ''); ?>><?php echo e(__('Account/Personal Error')); ?></option>
                    <option value="translation_error" <?php echo e(old('error_type') === 'translation_error' ? 'selected' : ''); ?>><?php echo e(__('Translation Error')); ?></option>
                    <option value="performance_issue" <?php echo e(old('error_type') === 'performance_issue' ? 'selected' : ''); ?>><?php echo e(__('Performance Issue')); ?></option>
                    <option value="bug_report" <?php echo e(old('error_type') === 'bug_report' ? 'selected' : ''); ?>><?php echo e(__('Bug Report')); ?></option>
                    <option value="feature_request" <?php echo e(old('error_type') === 'feature_request' ? 'selected' : ''); ?>><?php echo e(__('Feature Request')); ?></option>
                    <option value="other" <?php echo e(old('error_type') === 'other' ? 'selected' : ''); ?>><?php echo e(__('Other')); ?></option>
                </select>
                <p style="margin:8px 0 0; font-size:13px; color:var(--c-on-surface-2);"><?php echo e(__('Please select the category that best describes your issue')); ?></p>
            </div>

            <div>
                <label for="subject" style="display:block; margin-bottom:6px; font-weight:600;"><?php echo e(__('Title')); ?></label>
                <input 
                    type="text" 
                    id="subject" 
                    name="subject" 
                    value="<?php echo e(old('subject')); ?>" 
                    required
                    maxlength="255"
                    placeholder="<?php echo e(__('Brief title for your report...')); ?>"
                    style="width:100%; padding:10px 12px; border:1px solid var(--c-border); border-radius:8px; background:var(--c-surface); color:var(--c-on-surface);"
                >
                <p style="margin:8px 0 0; font-size:13px; color:var(--c-on-surface-2);"><?php echo e(__('A short, descriptive title for your issue')); ?></p>
            </div>

            <div>
                <label for="description" style="display:block; margin-bottom:6px; font-weight:600;"><?php echo e(__('Description')); ?></label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="12" 
                    required
                    maxlength="2000"
                    placeholder="<?php echo e(__('Please describe the issue you\'re experiencing in detail (up to 400 words)...')); ?>"
                    style="width:100%; padding:10px 12px; border:1px solid var(--c-border); border-radius:8px; background:var(--c-surface); color:var(--c-on-surface); font-family:inherit; resize:vertical;"
                ><?php echo e(old('description')); ?></textarea>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px;">
                    <p style="margin:0; font-size:13px; color:var(--c-on-surface-2);"><?php echo e(__('Maximum 400 words. Please provide as much detail as possible.')); ?></p>
                    <span id="word-count" style="font-size:13px; color:var(--c-on-surface-2);"><?php echo e(__('0 words')); ?></span>
                </div>
            </div>

            <div style="display:flex; gap:12px; margin-top:8px;">
                <button type="submit" class="btn btn-primary"><?php echo e(__('Submit Report')); ?></button>
                <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-outline"><?php echo e(__('Cancel')); ?></a>
            </div>
        </div>
    </form>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('description');
    const wordCount = document.getElementById('word-count');
    const wordLabel = "<?php echo e(__('words')); ?>";
    
    function updateWordCount() {
        const text = textarea.value.trim();
        const words = text ? text.split(/\s+/).filter(word => word.length > 0) : [];
        const count = words.length;
    wordCount.textContent = count + ' ' + wordLabel;
        
        if (count > 400) {
            wordCount.style.color = '#ef4444';
        } else if (count > 350) {
            wordCount.style.color = '#f59e0b';
        } else {
            wordCount.style.color = 'var(--c-on-surface-2)';
        }
    }
    
    textarea.addEventListener('input', updateWordCount);
    updateWordCount();
});
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Nosleguma-darbs-investacija\resources\views/tickets/create.blade.php ENDPATH**/ ?>