<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ticket_id',
        'user_id',
        'content',
        'is_from_admin',
        'email_message_id', // ID del mensaje de correo relacionado
        'source',          // web, email, api
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_from_admin' => 'boolean',
    ];
    
    /**
     * El método boot se ejecuta cuando se carga el modelo
     */
    protected static function booted()
    {
        static::created(function ($reply) {
            // Procesar menciones
            $mentionedUsers = self::extractMentions($reply->content);
            if (!empty($mentionedUsers)) {
                $reply->processMentions($mentionedUsers);
            }
        });
    }
    
    /**
     * Extraer los nombres de usuario mencionados en el contenido
     * 
     * @param string $content
     * @return array
     */
    public static function extractMentions($content)
    {
        // Extraer @username del contenido
        preg_match_all('/@([a-zA-Z0-9_]+)/', $content, $matches);
        
        if (!empty($matches[1])) {
            return $matches[1];
        }
        
        return [];
    }
    
    /**
     * Procesar las menciones encontradas en el contenido
     * 
     * @param array $usernames
     */
    public function processMentions(array $usernames)
    {
        // Buscar usuarios mencionados
        $users = User::whereIn('name', $usernames)
            ->whereIn('role', ['admin', 'super_admin', 'support'])
            ->get();
        
        // Registrar menciones
        foreach ($users as $user) {
            Mention::create([
                'user_id' => $user->id,
                'mentionable_id' => $this->id,
                'mentionable_type' => self::class,
                'mentioned_by' => $this->user_id
            ]);
            
            // Notificar al usuario mencionado
            if (class_exists('\App\Notifications\YouWereMentioned')) {
                $user->notify(new \App\Notifications\YouWereMentioned($this->ticket, $this));
            }
        }
    }

    /**
     * Get all the mentions in this reply.
     */
    public function mentions()
    {
        return $this->morphMany(Mention::class, 'mentionable');
    }
    
    /**
     * Get the ticket that owns the reply.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the user that owns the reply.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the attachments for this reply.
     */
    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class)->where('context', 'reply');
    }
}
