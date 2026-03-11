// Run this code in your browser console while logged into the bank reconciliation page
// It will call the diagnostic endpoint and show the results

(function() {
    const params = {
        transfer_fee_id: 447,
        trx_id: 'DP001-0825',
        date_from: '2025-09-01',
        date_to: '2025-09-30',
        transaction_type: 2, // 2 = OUT (as shown in your screenshot)
        is_recon: 1 // 1 = Yes
    };
    
    // Get CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    
    // Make POST request
    fetch('/investigateTransferFeeDiscrepancy', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: JSON.stringify(params)
    })
    .then(response => response.json())
    .then(data => {
        console.log('=== TRANSFER FEE DISCREPANCY INVESTIGATION ===');
        console.log('\n1. TRANSFER FEE MAIN:');
        console.log('   Amount:', data.transfer_fee_main?.transfer_amount);
        console.log('   Transaction ID:', data.transfer_fee_main?.transaction_id);
        
        console.log('\n2. TRANSFER FEE DETAILS (Calculated):');
        console.log('   Total:', data.transfer_fee_details?.calculated_total);
        console.log('   Breakdown:', data.transfer_fee_details);
        
        console.log('\n3. LEDGER ENTRIES (Bank Recon Total):');
        console.log('   Total:', data.ledger_entries?.total);
        console.log('   Breakdown by type:', data.ledger_entries?.breakdown);
        console.log('   Entry count:', data.ledger_entries?.all_entries_count);
        
        console.log('\n4. LINKED TO TRANSFER FEE DETAILS:');
        console.log('   Total:', data.linked_to_details?.total);
        console.log('   Breakdown:', data.linked_to_details?.breakdown);
        
        console.log('\n5. UNLINKED ENTRIES (The Problem!):');
        console.log('   Count:', data.unlinked_entries?.count);
        console.log('   Total:', data.unlinked_entries?.total);
        console.log('   Entries:', data.unlinked_entries?.entries);
        
        console.log('\n6. DISCREPANCY SUMMARY:');
        console.log('   Transfer Fee Total:', data.discrepancy?.transfer_fee_total);
        console.log('   Ledger Total:', data.discrepancy?.ledger_total);
        console.log('   Difference:', data.discrepancy?.difference);
        console.log('   Linked Total:', data.discrepancy?.linked_total);
        console.log('   Unlinked Total:', data.discrepancy?.unlinked_total);
        
        console.log('\n=== FULL RESPONSE ===');
        console.log(JSON.stringify(data, null, 2));
        
        // Show alert with key findings
        if (data.unlinked_entries?.count > 0) {
            alert(`Found ${data.unlinked_entries.count} unlinked entries totaling ${data.unlinked_entries.total}. This is likely the cause of the discrepancy!`);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error calling diagnostic endpoint: ' + error.message);
    });
})();
