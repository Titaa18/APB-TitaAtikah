---
description: Automatically stage all changes, commit with a descriptive message, and push to the main branch on GitHub.
---

1. Stage all changes
// turbo
run_command(CommandLine="git add .", Cwd="c:\\xampp\\htdocs\\APB-Tita", SafeToAutoRun=true)

2. Create a commit with a timestamped message
// turbo
run_command(CommandLine="git commit -m \"Auto-update: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')\"", Cwd="c:\\xampp\\htdocs\\APB-Tita", SafeToAutoRun=true)

3. Push to the main branch
// turbo
run_command(CommandLine="git push origin main", Cwd="c:\\xampp\\htdocs\\APB-Tita", SafeToAutoRun=true)
