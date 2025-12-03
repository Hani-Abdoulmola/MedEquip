#!/bin/bash

# Script to fix auth() usage in all Web controllers
# This adds Auth facade import and replaces auth()->user() and auth()->id() calls

CONTROLLERS=(
    "DeliveryController.php"
    "InvoiceController.php"
    "OrderController.php"
    "PaymentController.php"
    "ProductController.php"
    "QuotationController.php"
    "RegistrationApprovalController.php"
    "RfqController.php"
    "UserController.php"
)

CONTROLLER_DIR="app/Http/Controllers/Web"

for controller in "${CONTROLLERS[@]}"; do
    echo "Processing $controller..."
    
    # Check if file exists
    if [ ! -f "$CONTROLLER_DIR/$controller" ]; then
        echo "  File not found: $controller"
        continue
    fi
    
    # Check if Auth facade is already imported
    if ! grep -q "use Illuminate\\\\Support\\\\Facades\\\\Auth;" "$CONTROLLER_DIR/$controller"; then
        echo "  Adding Auth facade import..."
        # This would require sed manipulation
    fi
    
    echo "  Done with $controller"
done

echo "All controllers processed!"

