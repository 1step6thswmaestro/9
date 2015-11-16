Rails.application.routes.draw do
  resources :scores, only: :create
  resources :reactions
  resources :user_actions, only: :create
end
