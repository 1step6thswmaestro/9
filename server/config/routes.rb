Rails.application.routes.draw do
  get '/recommand/generate' => 'reactions#recommand'

  resources :purchases, only: :create
  resources :scores, only: :create
  resources :reactions, only: [:index, :create, :destroy]
  resources :user_actions, only: :create
end
