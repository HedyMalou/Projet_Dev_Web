#!/bin/bash
# git-sync.sh — Gère les permissions avant/après les opérations Git
# Usage : ./git-sync.sh pull   ou   ./git-sync.sh push

ACTION=$1

if [ -z "$ACTION" ]; then
    echo "Usage : ./git-sync.sh pull | push | status"
    exit 1
fi

PROJECT_DIR="/home/cytech/Projet_Dev_Web"

echo ">>> Passage des droits à cytech..."
sudo chown -R cytech:cytech "$PROJECT_DIR"
sudo chown -R cytech:cytech "$PROJECT_DIR/.git"

case "$ACTION" in
    pull)
        echo ">>> git pull..."
        git -C "$PROJECT_DIR" pull origin main
        ;;
    push)
        echo ">>> git add + commit + push..."
        git -C "$PROJECT_DIR" add -A
        read -p "Message de commit : " MSG
        git -C "$PROJECT_DIR" commit -m "$MSG"
        git -C "$PROJECT_DIR" push origin main
        ;;
    status)
        git -C "$PROJECT_DIR" status
        ;;
    *)
        echo "Action inconnue : $ACTION (pull | push | status)"
        ;;
esac

echo ">>> Restauration des droits Apache (www-data)..."
sudo chown -R www-data:www-data "$PROJECT_DIR"

echo ">>> Terminé."
