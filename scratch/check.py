with open('customer/receipt.php', 'r', encoding='utf-8') as f:
    for i, line in enumerate(f):
        if 310 <= i + 1 <= 330:
            print(f"{i+1}: {line}", end='')

