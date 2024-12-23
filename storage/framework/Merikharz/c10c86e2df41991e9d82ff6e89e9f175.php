<?php echo $__env->make('Panel.Layout.head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->yieldContent('head'); ?>
<body class="overflow-x-hidden">
<?php echo $__env->make('Panel.Layout.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<main class="px-5 mt-4">
   <?php echo $__env->yieldContent('content'); ?>
</main>
<?php echo $__env->make('Panel.Layout.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('Panel.Layout.script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->yieldContent('script'); ?>
</body>
</html>
<?php /**PATH H:\arz\resources\Merikharz/Panel/Layout/master.blade.php ENDPATH**/ ?>